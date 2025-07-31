@php
    $initialNotifications = auth()->user()->notifications()
        ->orderByRaw('read_at IS NULL DESC')
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
    $unreadCount = auth()->user()->unreadNotifications()->count();
@endphp

<li class="nav-item dropdown-center">
    <a class="nav-link link-light position-relative" href="#" role="button" data-bs-toggle="dropdown"
       aria-expanded="false">
        <i class="fas fa-bell fa-lg text-white"></i>
        @if($unreadCount > 0)
            <div id="notification-badge" class="position-absolute top-0 start-100 translate-middle badge text-white border ms-1 mt-1"
                 style="font-size: 0.8rem; padding: 2px 4px;">
                {{ $unreadCount }}
                <div class="visually-hidden">непрочитанных уведомлений</div>
            </div>
        @endif
    </a>

    <div class="dropdown-menu dropdown-menu-end p-0 shadow"
         style="width: 380px; border-radius: 0.5rem; overflow: hidden;">
        <div class="notification-list" style="max-height: 340px; overflow-y: auto;">
            <div class="list-group list-group-flush" id="notification-list">
                @foreach($initialNotifications as $notification)
                    <x-notifications.notification-item :notification="$notification"/>
                @endforeach
            </div>
        </div>

        <div class="border-top p-2 text-center bg-white">
            <button class="btn btn-sm btn-outline-primary">@lang('Отметить всё прочитанным')</button>
        </div>
    </div>
</li>

@pushonce('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.dropdown-menu').forEach(function (dropdown) {
                dropdown.addEventListener('click', function (e) {
                    e.stopPropagation();
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const notificationList = document.getElementById('notification-list');
            if (notificationList) {
                notificationList.addEventListener('click', async function (e) {
                    const item = e.target.closest('.list-group-item');
                    if (!item || !item.classList.contains('unread')) return;

                    const notificationId = item.id;

                    const response = await fetch(`/notifications/${notificationId}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        item.classList.remove('unread', 'bg-primary', 'bg-opacity-10');
                        item.querySelector('.unread-indicator')?.remove();

                        const badge = document.getElementById('notification-badge');
                        if (badge) {
                            const currentCount = parseInt(badge.textContent) || 1;
                            const newCount = Math.max(currentCount - 1, 0);

                            if (newCount === 0) {
                                badge.remove();
                            } else {
                                badge.textContent = newCount;
                            }
                        }
                    }
                });
            }

            // Кнопка "отметить все"
            const markAllBtn = document.querySelector('.btn-outline-primary');
            if (markAllBtn) {
                markAllBtn.addEventListener('click', async () => {
                    const response = await fetch('/notifications/read-all', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        document.querySelectorAll('.list-group-item').forEach(item => {
                            item.classList.remove('bg-primary', 'bg-opacity-10', 'unread');
                            item.querySelector('.unread-indicator')?.remove();
                        });

                        const badge = document.getElementById('notification-badge');
                        if (badge) {
                            badge.remove();
                        }
                    }
                });
            }
        });


        let currentPage = 1;
        let isLoading = false;
        let hasMore = true;

        const notificationList = document.getElementById('notification-list');
        const container = document.querySelector('.notification-list');

        container.addEventListener('scroll', async () => {
            if (isLoading || !hasMore) return;

            const scrollBottom = container.scrollTop + container.clientHeight;
            const fullHeight = container.scrollHeight;

            if (scrollBottom >= fullHeight - 50) {
                isLoading = true;
                currentPage++;

                try {
                    const response = await fetch(`/notifications?page=${currentPage}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                    });

                    if (!response.ok) throw new Error('Ошибка загрузки');

                    const data = await response.json();

                    data.html.forEach(html => {
                        notificationList.insertAdjacentHTML('beforeend', html);
                    });


                    if (data.current_page >= data.last_page) {
                        hasMore = false;
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    isLoading = false;
                }
            }
        });
    </script>
@endpushonce

