@extends('layouts.main')

@section('title', $title)

@section('main.content')
    <div class="container @if(isset($selectedChat)) chat-selected @endif">
        <div class="row g-3 flex-md-nowrap">
            <!-- Список чатов -->
            <div id="chat-list-column" class="col-md-4 d-block d-md-block">
                <x-card class="card shadow" style="height: 80vh;">
                    <div class="card-header bg-primary bg-gradient text-white">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link active text-white" id="my-chats-tab" data-bs-toggle="tab"
                                   href="#my-chats" role="tab">Мои чаты</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" id="new-chats-tab" data-bs-toggle="tab" href="#new-chats"
                                   role="tab">Новые</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-0 tab-content" style="overflow-y: auto;">
                        <!-- Мои чаты -->
                        <div class="tab-pane fade show active" id="my-chats" role="tabpanel">
                            <ul class="list-group list-group-flush">
                                @foreach ($chats as $chat)
                                    <li class="list-group-item border-light-subtle d-flex align-items-center @if(isset($selectedChat) && $chat->id === $selectedChat->id) active @endif">
                                        <a href="{{ route('chat.show', $chat) }}" data-chat-id="{{ $chat->id }}"
                                           data-last-message-id="{{ $chat->lastMessage?->id }}"
                                           class="text-decoration-none d-flex align-items-center flex-grow-1 text-dark chat-link">
                                            <img src="{{ $chat->avatar_url ?? '/default-avatar.png' }}"
                                                 class="rounded-circle me-2" width="40" height="40" alt="avatar">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <strong>{{ $chat->name }}</strong>
                                                    <small>{{ $chat->lastMessage?->created_at->format('H:i') }}</small>
                                                </div>
                                                <div class="small">
                                                    @php
                                                        /** @var $chat \App\Models\Chat*/
                                                    @endphp
                                                    @if($chat->lastMessage)
                                                        <span class="me-2">
                                                        @if($chat->lastMessage->user_id === auth()->id())
                                                                @if($chat->lastMessage->reads->where('user_id', '!=', auth()->id())->count())
                                                                    <i class="fas fa-check-double text-primary"></i>
                                                                @else
                                                                    <i class="fas fa-check text-primary"></i>
                                                                @endif
                                                            @elseif(!$chat->lastMessage->reads->contains('user_id', auth()->id()))
                                                                <i class="fas fa-circle text-primary fa-2xs"></i>
                                                            @endif
                                                    </span>
                                                        {{ Str::limit($chat->lastMessage->text, 25) }}
                                                    @else
                                                        @lang('Еще нет сообщений')
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                        <div class="dropdown ms-2">
                                            <button class="btn btn-sm btn-link text-dark" type="button"
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#">Архивировать</a></li>
                                                <li><a class="dropdown-item" href="#">Удалить</a></li>
                                                <li><a class="dropdown-item" href="#">Отметить как непрочитанное</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <!-- Новые чаты -->
                        <div class="tab-pane fade" id="new-chats" role="tabpanel">
                            <ul class="list-group list-group-flush">
                                @foreach ($newChats as $chat)
                                    <li class="list-group-item d-flex align-items-center @if(isset($selectedChat) && $chat->id === $selectedChat->id) active @endif">
                                        <a href="{{ route('chat.show', $chat) }}"
                                           class="text-decoration-none d-flex align-items-center flex-grow-1 text-dark chat-link">
                                            <img
                                                src="{{ $chat->users->first(fn($u) => $u->id != auth()->id())->avatar ?? '/default-avatar.png' }}"
                                                class="rounded-circle me-2" width="40" height="40" alt="avatar">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <strong>{{ $chat->name }}</strong>
                                                    <small>{{ $chat->lastMessage?->created_at->format('H:i') }}</small>
                                                </div>
                                                <div class="small">
                                                    {{ Str::limit($chat->lastMessage->text, 25) }}
                                                    <span class="badge bg-primary text-white ms-2">!</span>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="dropdown ms-2">
                                            <button class="btn btn-sm btn-link text-dark" type="button"
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('chat.accept', $chat) }}">Принять</a>
                                                </li>
                                                <li><a class="dropdown-item" href="#">Отклонить</a></li>
                                                <li><a class="dropdown-item" href="#">Заблокировать</a></li>
                                            </ul>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Чат -->
            <div id="chat-column" class="col-md-8 d-none d-md-block">
                <div class="card shadow" style="height: 80vh;">
                    @if (isset($selectedChat))
                        <div
                            class="card-header bg-primary bg-gradient text-white d-flex align-items-center justify-content-between">
                            <!-- Кнопка назад -->
                            <button id="back-button" class="btn btn-link text-white d-md-none">
                                <i class="fas fa-arrow-left"></i>
                            </button>
                            <h5 class="mb-0 flex-grow-1">
                                {{ $selectedChat->name ?? $selectedChat->users->first(fn($u) => $u->id != auth()->id())->name }}
                            </h5>
                            <div class="d-none d-md-block">
                                <button class="btn btn-sm btn-link text-white" title="Информация о чате">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                                <button class="btn btn-sm btn-link text-white" title="Очистить чат">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="btn btn-sm btn-link text-white" title="Дополнительно">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column p-3" id="chat-body" data-chat-id="{{ $selectedChat->id }}" style="overflow-y: auto;">
                            <div id="sentinel-top"></div>
                            @foreach ($initialMessages as $message)
                                <div id="message{{ $message->id }}"
                                     class="message-wrapper
                                    @if($message->user_id === auth()->id())
                                        sent
                                    @else
                                        received
                                    @endif
                                    @if($message->user_id !== auth()->id() && !$message->reads->contains('user_id', auth()->id()))
                                        unread
                                    @endif
                                        ">
                                    <div
                                        class="message-bubble @if($message->user_id === auth()->id()) sent @else received @endif">
                                        <div class="fw-bold small">
                                            {{ $message->user_name }}
                                        </div>
                                        <div class="my-1">{{ $message->text }}</div>
                                        <div class="message-meta">
                                            {{ $message->created_at->format('H:i') }}
                                            @if($message->user_id === auth()->id())
                                                <span class="read-indicator" data-message-id="{{ $message->id }}"
                                                      data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true">
                                                    @if($message->isRead())
                                                        <i class="fas fa-check-double ms-1"></i>
                                                    @else
                                                        <i class="fas fa-check ms-1"></i>
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($message->user_id !== auth()->id() && !$message->reads->contains('user_id', auth()->id()))
                                        <div class="ms-3 d-flex align-items-center unread-message">
                                            <i class="fas fa-circle text-primary fa-2xs"></i>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                            <div id="sentinel-bottom"></div>
                        </div>
                        <div class="card-footer border-light-subtle bg-light">
                            <form id="chat-message-form" action="{{ route('chat.message.store', $selectedChat) }}"
                                  method="POST">
                                @csrf
                                <div class="input-group rounded border border-light-subtle">
                                    <textarea id="chat-message-textarea" name="text" class="form-control border-0"
                                              placeholder="Введите сообщение..." required
                                              style="resize: none; min-height: 40px; max-height: 100px;"></textarea>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="card-body d-flex align-items-center justify-content-center text-muted">
                            Выберите чат из списка слева
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@pushonce('js')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const chatListColumn = document.getElementById('chat-list-column');
            const chatColumn = document.getElementById('chat-column');
            const backButton = document.getElementById('back-button');
            const currentChatId = {{ $selectedChat->id ?? 'null' }};

            function isMobile() {
                return window.innerWidth < 768;
            }

            function showChat() {
                if (isMobile()) {
                    chatListColumn?.classList.add('d-none');
                    chatColumn?.classList.remove('d-none');
                }
            }

            function showList() {
                if (isMobile()) {
                    chatColumn?.classList.add('d-none');
                    chatListColumn?.classList.remove('d-none');
                }
            }

            if (backButton) {
                backButton.addEventListener('click', function () {
                    showList();
                });
            }

            document.querySelectorAll('.chat-link').forEach(link => {
                link.addEventListener('click', function (e) {
                    const clickedChatId = parseInt(link.dataset.chatId);

                    if (clickedChatId === currentChatId) {
                        e.preventDefault();
                        showChat();
                    }
                });
            });

            if (currentChatId) {
                showChat();
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('chat-message-form');
            const textarea = document.getElementById('chat-message-textarea');
            const chatBody = document.getElementById('chat-body');
            const sentinelTop = document.getElementById('sentinel-top');
            const sentinelBottom = document.getElementById('sentinel-bottom');
            const chatId = {{ $selectedChat->id ?? 'null' }};
            const currentUserId = {{ auth()->id() }};

            let loading = false;
            let isInitialLoad = false;

            // Инициализация наблюдателя за сообщениями
            if (chatBody && chatId && window.initMessageObserver) {
                window.initMessageObserver(chatBody, chatId);
                console.log('messageObserver инициализирован для chatId:', chatId);
            } else {
                console.warn('Не удалось инициализировать messageObserver:', { chatBody, chatId, initMessageObserver: !!window.initMessageObserver });
            }

            // Функция для обновления превью чата
            function updateChatPreview(chatId, messageId, messageText, createdAt) {
                const myChatsList = document.querySelector('#my-chats ul.list-group');
                if (!myChatsList) {
                    console.error('Список #my-chats не найден');
                    return;
                }

                const chatElement = myChatsList.querySelector(`a[data-chat-id="${chatId}"]`)?.closest('li');
                if (!chatElement) {
                    console.warn(`Чат с ID ${chatId} не найден в списке`);
                    return;
                }

                // Обновляем текст и время последнего сообщения
                chatElement.querySelector('.small').innerHTML = `
                    <span class="me-2">
                        <i class="fas fa-check text-primary"></i>
                    </span>
                    ${messageText.slice(0, 25)}${messageText.length > 25 ? '...' : ''}
                `;
                chatElement.querySelector('small').textContent = createdAt;

                // Обновляем data-last-message-id
                const chatLink = chatElement.querySelector('a');
                chatLink.dataset.lastMessageId = messageId;

                // Перемещаем чат в начало списка
                myChatsList.prepend(chatElement);
            }

            const loadMoreMessages = async (direction) => {
                if (loading || !chatId || !isInitialLoad) {
                    return;
                }
                loading = true;

                const messages = chatBody.querySelectorAll('.message-wrapper');

                if (messages.length === 0) {
                    loading = false;
                    return;
                }

                const lastMessage = direction === 'older' ? messages[0] : messages[messages.length - 1];
                const lastId = lastMessage ? lastMessage.id.replace('message', '') : null;

                if (!lastId) {
                    console.error('lastId отсутствует');
                    loading = false;
                    return;
                }

                try {
                    const url = `/chat/${chatId}/load-more?last_id=${lastId}&direction=${direction}`;

                    const response = await fetch(url, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });

                    if (!response.ok) throw new Error(`Ошибка загрузки сообщений: ${response.statusText}`);

                    const data = await response.json();
                    const newMessages = data.data;

                    let scrollPosition = chatBody.scrollTop;
                    let scrollHeightBefore = chatBody.scrollHeight;

                    newMessages.forEach(msg => {
                        const bubble = document.createElement('div');
                        bubble.id = `message${msg.id}`;
                        bubble.className = `message-wrapper ${msg.user_id === currentUserId ? 'sent' : 'received'} ${!msg.is_read && msg.user_id !== currentUserId ? 'unread' : ''}`;
                        bubble.innerHTML = `
                            <div class="message-bubble ${msg.user_id === currentUserId ? 'sent' : 'received'}">
                                <div class="fw-bold small">${msg.user_name}</div>
                                <div class="my-1">${msg.text}</div>
                                <div class="message-meta">
                                    ${msg.created_at}
                                    ${msg.user_id === currentUserId ? `
                                        <span class="read-indicator" data-message-id="${msg.id}" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true">
                                            ${msg.is_read ? '<i class="fas fa-check-double ms-1"></i>' : '<i class="fas fa-check ms-1"></i>'}
                                        </span>
                                    ` : ''}
                                </div>
                            </div>
                            ${msg.user_id !== currentUserId && !msg.is_read ? '<div class="ms-3 d-flex align-items-center unread-message"><i class="fas fa-circle text-primary fa-2xs"></i></div>' : ''}
                        `;

                        if (direction === 'older') {
                            chatBody.insertBefore(bubble, sentinelTop.nextSibling);
                        } else {
                            chatBody.insertBefore(bubble, sentinelBottom);
                        }

                        if (!msg.is_read && msg.user_id !== currentUserId && window.messageObserver) {
                            window.messageObserver.observe(bubble);
                        }
                    });

                    if (direction === 'older' && newMessages.length > 0) {
                        const scrollHeightAfter = chatBody.scrollHeight;
                        chatBody.scrollTop = scrollPosition + (scrollHeightAfter - scrollHeightBefore);
                    }

                    // Инициализация popover для новых сообщений
                    document.querySelectorAll('.read-indicator').forEach(indicator => {
                        if (indicator.dataset.popoverInitialized) return;

                            const messageId = indicator.dataset.messageId;
                            new bootstrap.Popover(indicator, {
                                title: 'Прочитано',
                                content: 'Загрузка...',
                                placement: 'top',
                                html: true,
                                trigger: 'hover'
                            });
                            let fetchedContent = null;
                            indicator.addEventListener('shown.bs.popover', async () => {
                                if (fetchedContent) {
                                    const popoverInstance = bootstrap.Popover.getInstance(indicator);
                                    const tip = popoverInstance ? popoverInstance._getTipElement() : null;
                                    if (tip) tip.querySelector('.popover-body').innerHTML = fetchedContent;
                                    return;
                                }

                                try {
                                    const response = await fetch(`/chat/${chatId}/message/${messageId}/reads`);
                                    const data = await response.json();
                                    fetchedContent = data.length === 0
                                        ? 'Еще никто не прочитал'
                                        : data.map(read => `${read.user_name} в ${read.read_at}`).join('<br>');

                                    const popoverInstance = bootstrap.Popover.getInstance(indicator);
                                    const tip = popoverInstance ? popoverInstance._getTipElement() : null;
                                    if (tip) tip.querySelector('.popover-body').innerHTML = fetchedContent;
                                } catch {
                                    fetchedContent = 'Ошибка загрузки';
                                    const popoverInstance = bootstrap.Popover.getInstance(indicator);
                                    const tip = popoverInstance ? popoverInstance._getTipElement() : null;
                                    if (tip) tip.querySelector('.popover-body').innerHTML = fetchedContent;
                                }
                            });


                        indicator.dataset.popoverInitialized = true;
                    });


                } catch (err) {
                    console.error('Ошибка в loadMoreMessages:', err);
                } finally {
                    loading = false;
                }
            };

            const observerTop = new IntersectionObserver(entries => {
                if (entries[0].isIntersecting) {
                    loadMoreMessages('older');
                }
            }, { threshold: 1.0 });

            if (sentinelTop) observerTop.observe(sentinelTop);

            const observerBottom = new IntersectionObserver(entries => {
                if (entries[0].isIntersecting) {
                    loadMoreMessages('newer');
                }
            }, { threshold: 1.0 });

            if (sentinelBottom) observerBottom.observe(sentinelBottom);

            const scrollToInitialPosition = () => {
                setTimeout(() => {
                    const firstUnread = chatBody.querySelector('.message-wrapper.unread');

                    if (firstUnread) {
                        const offsetTop = firstUnread.offsetTop;
                        const messageHeight = firstUnread.offsetHeight;
                        const containerHeight = chatBody.clientHeight;

                        const scrollTarget = offsetTop - (containerHeight / 2) + (messageHeight / 2);

                        chatBody.scrollTo({
                            top: scrollTarget,
                            behavior: 'smooth'
                        });
                    } else {
                        chatBody.scrollTo({
                            top: chatBody.scrollHeight,
                            behavior: 'smooth'
                        });
                    }
                    isInitialLoad = true;
                }, 100);
            };

            requestAnimationFrame(scrollToInitialPosition);

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                const formData = new FormData(form);
                const text = formData.get('text');

                if (!text.trim()) return;

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    if (!response.ok) throw new Error('Ошибка отправки сообщения');

                    const data = await response.json();

                    textarea.value = '';

                    // Добавляем сообщение в чат
                    const bubble = document.createElement('div');
                    bubble.id = `message${data.id}`;
                    bubble.className = 'message-wrapper sent';
                    bubble.innerHTML = `
                        <div class="message-bubble sent">
                            <div class="fw-bold small">${data.user_name}</div>
                            <div class="my-1">${data.text}</div>
                            <div class="message-meta">
                                ${data.created_at}
                                <span class="read-indicator" data-message-id="${data.id}" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true">
                                <i class="fas fa-check ms-1"></i>
                                </span>
                            </div>
                        </div>
                    `;
                    chatBody.insertBefore(bubble, sentinelBottom);
                    chatBody.scrollTop = chatBody.scrollHeight;

                    new bootstrap.Popover(bubble.querySelector('.read-indicator'), {
                        title: 'Прочитано',
                        content: 'Загрузка...',
                        placement: 'top',
                        html: true,
                    });

                    updateChatPreview(chatId, data.id, data.text, data.created_at);
                } catch (err) {
                    console.error(err);
                    showErrorToast('Не удалось отправить сообщение');
                }
            });

            // Инициализация popover для существующих сообщений
            document.querySelectorAll('.read-indicator').forEach(indicator => {
                const messageId = indicator.dataset.messageId;
                new bootstrap.Popover(indicator, {
                    title: 'Прочитано',
                    content: 'Загрузка...',
                    placement: 'top',
                    html: true,
                });
            });

            // Обработка события прочтения в реальном времени
            window.addEventListener('message-read', (event) => {
                const { chatId: eventChatId, messageId } = event.detail;
                if (parseInt(chatId) === eventChatId) {
                    const messageElement = document.getElementById(`message${messageId}`);
                    if (messageElement) {
                        const indicator = messageElement.querySelector('.read-indicator');
                        if (indicator) {
                            indicator.innerHTML = '<i class="fas fa-check-double ms-1"></i>';
                        }
                    }
                }
            });
        });
    </script>
@endpushonce

@pushonce('css')
    <style>
        .message-bubble {
            max-width: 70%;
            padding: 0.75rem 1rem;
            border-radius: 1.25rem;
            font-size: 0.95rem;
            line-height: 1.5;
            word-wrap: break-word;
            position: relative;
        }

        .message-bubble.sent {
            background-color: rgba(255, 211, 218, 0.4);
            color: #212529;
            border-bottom-right-radius: 0.4rem;
        }

        .message-bubble.received {
            background-color: rgba(126, 67, 13, 0.10);
            color: #212529;
            border-bottom-left-radius: 0.4rem;
        }

        .message-bubble .message-meta {
            font-size: 0.75rem;
            margin-top: 0.4rem;
            opacity: 0.7;
        }

        .message-bubble.sent .message-meta {
            text-align: right;
        }

        .message-bubble.received .message-meta {
            text-align: left;
        }

        .message-wrapper {
            display: flex;
            margin-bottom: 1rem;
        }

        .message-wrapper.sent {
            justify-content: flex-end;
        }

        .message-wrapper.received {
            justify-content: flex-start;
        }

        .list-group-item.active {
            background-color: rgba(161, 47, 74, 0.1);
        }

        .list-group-item:hover {
            background-color: rgba(161, 47, 74, 0.1);
        }

        .card {
            border: none;
            border-radius: 0.75rem;
        }

        .card-header {
            border-bottom: 2px solid rgba(0, 0, 0, 0);
        }

        .nav-tabs .nav-link.active {
            color: #ffffff;
            border-color: transparent;
            border-bottom: 2px solid #ffffff;
            background: transparent;
        }

        .nav-tabs .nav-link {
            color: rgba(255, 255, 255, 0.8);
        }

        .nav-tabs .nav-link:hover {
            color: #ffffff;
            border-color: transparent;
            border-bottom: 2px solid #ffffff;
        }

        .bg-primary {
            background-color: #a12f4a !important;
        }

        .text-primary {
            color: #a12f4a !important;
        }

        #chat-body {
            overflow-y: auto;
            max-height: 80vh;
            position: relative;
            flex: 1;
            padding: 0.75rem;
        }

        .popover {
            border-radius: 0.75rem;
        }

        .popover-header {
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }

        .popover-body {
            border-bottom-left-radius: 0.75rem;
            border-bottom-right-radius: 0.75rem;
        }

        @media (max-width: 767.98px) {
            .messages-panel {
                display: none;
            }

            .chat-selected .chat-panel {
                display: none;
            }

            .chat-selected .messages-panel {
                display: block;
            }

            .col-md-4, .col-md-8 {
                width: 100%;
                padding: 0;
                margin: 0;
            }

            .card-body {
                padding: 0.5rem;
            }

            .message-bubble {
                max-width: 85%;
                padding: 0.5rem 0.75rem;
            }

            .card-footer {
                position: sticky;
                bottom: 0;
                z-index: 10;
                background-color: #f8f9fa;
            }

            .container {
                max-width: 100%;
                padding: 0;
            }
        }

        @media (min-width: 768px) {
            .chat-panel, .messages-panel {
                display: block !important;
            }
        }
    </style>
@endpushonce
