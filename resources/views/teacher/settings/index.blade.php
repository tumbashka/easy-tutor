@extends('layouts.main')

@section('title', 'Настройки')

@section('main.content')
    <x-form-container>
        <!-- Список предметов -->
        <x-card>
            <x-card.header :title="'Список предметов'"/>
            <x-card.body>
                <table class="table table-hover" id="subjectsTable">
                    <tbody>
                    @foreach($subjects as $subject)
                        <tr id="subjectRow{{ $subject->id }}">
                            <td>{{ $subject->name }} {!!   $subject->is_default ? '<span class="badge rounded-pill text-bg-success fw-normal text-white">По умолчанию</span>' : '' !!}</td>
                            <td>
                                <x-icon-modal-action
                                    :action="route('user.settings.update-subject', $subject->id)"
                                    :method="'PUT'"
                                    :icon="'edit'"
                                    :text_btn="'Сохранить'"
                                    :text_head="'Редактирование'"
                                >
                                    <div class="mb-3">
                                        <label class="form-label">Название предмета</label>
                                        <input type="text" class="form-control"
                                               name="name" value="{{ $subject->name }}" required>
                                    </div>
                                    <div class="form-check form-switch">
                                        <label class="form-label">Предмет по умолчанию</label>
                                        <input class="form-check-input" type="checkbox" value="1"
                                               name="is_default" {{ $subject->is_default ? 'checked disabled' : '' }}>
                                    </div>
                                </x-icon-modal-action>

                                <x-icon-modal-action :action="route('user.settings.delete-subject', $subject->id)">
                                    Удалить предмет?
                                </x-icon-modal-action>
                            </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>
            </x-card.body>
        </x-card>

        <!-- Добавить предмет -->
        <x-card>
            <form action="{{ route('user.settings.store-subject') }}" method="POST">
                @csrf
                <x-card.header :title="'Добавить предмет'"/>
                <x-card.body>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="newSubject" name="name"
                               placeholder="Введите название предмета" required>
                        <div id="subjectError" class="text-danger mt-1" style="display: none;">
                            <ul class="mb-0" id="errorList"></ul>
                        </div>
                    </div>
                </x-card.body>
                <x-card.footer>
                    <x-button type="submit">Добавить</x-button>
                </x-card.footer>
            </form>
        </x-card>

        <!-- Настройки уведомлений -->
        <x-card>
            <x-card.header :title="'Уведомления'"/>
            <x-card.body>
                <form id="notificationsForm" action="" method="POST">
                    @csrf
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="emailNotifications"
                               name="email_notifications" {{ $user?->email_notifications ? 'checked' : '' }}>
                        <label class="form-check-label" for="emailNotifications">Получать уведомления по email</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="smsNotifications"
                               name="sms_notifications" {{ $user?->sms_notifications ? 'checked' : '' }}>
                        <label class="form-check-label" for="smsNotifications">Получать SMS-уведомления</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="pushNotifications"
                               name="push_notifications" {{ $user?->push_notifications ? 'checked' : '' }}>
                        <label class="form-check-label" for="pushNotifications">Получать push-уведомления</label>
                    </div>
                    <x-button type="submit">Сохранить настройки</x-button>
                </form>
            </x-card.body>
        </x-card>
    </x-form-container>
@endsection

@pushonce('js')
    {{--    <script>--}}
    {{--        document.addEventListener('DOMContentLoaded', function () {--}}
    {{--            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;--}}

    {{--            // Добавление предмета--}}
    {{--            document.getElementById('addSubjectForm').addEventListener('submit', function (e) {--}}
    {{--                e.preventDefault();--}}

    {{--                const form = this;--}}
    {{--                const formData = new FormData(form);--}}
    {{--                const newSubjectInput = document.getElementById('newSubject');--}}
    {{--                const subjectError = document.getElementById('subjectError');--}}
    {{--                const errorList = document.getElementById('errorList');--}}
    {{--                const subjectsTableBody = document.getElementById('subjectsTable').querySelector('tbody');--}}

    {{--                subjectError.style.display = 'none';--}}
    {{--                errorList.innerHTML = '';--}}

    {{--                fetch(form.action, {--}}
    {{--                    method: 'POST',--}}
    {{--                    body: formData,--}}
    {{--                    headers: {--}}
    {{--                        'X-Requested-With': 'XMLHttpRequest',--}}
    {{--                        'Accept': 'application/json'--}}
    {{--                    }--}}
    {{--                })--}}
    {{--                    .then(res => res.json())--}}
    {{--                    .then(data => {--}}
    {{--                        if (data.status === 'success') {--}}
    {{--                            const subject = data.subject;--}}

    {{--                            // Добавить строку--}}
    {{--                            const newRow = document.createElement('tr');--}}
    {{--                            newRow.id = `subjectRow${subject.id}`;--}}
    {{--                            newRow.innerHTML = `--}}
    {{--                            <td>${subject.name}</td>--}}
    {{--                            <td>--}}
    {{--                                <button class="btn btn-sm btn-outline-primary me-2"--}}
    {{--                                        data-bs-toggle="modal"--}}
    {{--                                        data-bs-target="#editSubjectModal${subject.id}">--}}
    {{--                                    Редактировать--}}
    {{--                                </button>--}}
    {{--                                <button class="btn btn-sm btn-outline-danger delete-subject-btn"--}}
    {{--                                        data-id="${subject.id}"--}}
    {{--                                        data-url="/user/settings/subject/${subject.id}">--}}
    {{--                                    Удалить--}}
    {{--                                </button>--}}
    {{--                            </td>`;--}}
    {{--                            subjectsTableBody.appendChild(newRow);--}}

    {{--                            // Добавить модалку--}}
    {{--                            const modal = document.createElement('div');--}}
    {{--                            modal.className = 'modal fade';--}}
    {{--                            modal.id = `editSubjectModal${subject.id}`;--}}
    {{--                            modal.tabIndex = -1;--}}
    {{--                            modal.setAttribute('aria-labelledby', `editSubjectModalLabel${subject.id}`);--}}
    {{--                            modal.setAttribute('aria-hidden', 'true');--}}
    {{--                            modal.innerHTML = `--}}
    {{--                            <div class="modal-dialog">--}}
    {{--                                <div class="modal-content">--}}
    {{--                                    <form class="edit-subject-form"--}}
    {{--                                          data-id="${subject.id}"--}}
    {{--                                          data-url="/user/settings/subject/${subject.id}">--}}
    {{--                                        <div class="modal-header">--}}
    {{--                                            <h5 class="modal-title">Редактировать предмет</h5>--}}
    {{--                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>--}}
    {{--                                        </div>--}}
    {{--                                        <div class="modal-body">--}}
    {{--                                            <div class="mb-3">--}}
    {{--                                                <label class="form-label">Название предмета</label>--}}
    {{--                                                <input type="text" class="form-control" name="name" value="${subject.name}" required>--}}
    {{--                                            </div>--}}
    {{--                                        </div>--}}
    {{--                                        <div class="modal-footer">--}}
    {{--                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>--}}
    {{--                                            <button type="submit" class="btn btn-primary">Сохранить</button>--}}
    {{--                                        </div>--}}
    {{--                                    </form>--}}
    {{--                                </div>--}}
    {{--                            </div>`;--}}
    {{--                            document.body.appendChild(modal);--}}

    {{--                            form.reset();--}}
    {{--                            showSuccessToast('Предмет добавлен');--}}
    {{--                        } else if (data.errors) {--}}
    {{--                            subjectError.style.display = 'block';--}}
    {{--                            Object.values(data.errors).forEach(error => {--}}
    {{--                                const li = document.createElement('li');--}}
    {{--                                li.textContent = error;--}}
    {{--                                errorList.appendChild(li);--}}
    {{--                            });--}}
    {{--                        }--}}
    {{--                    })--}}
    {{--                    .catch(() => showErrorToast('Ошибка при добавлении'));--}}
    {{--            });--}}

    {{--            // Редактирование предмета--}}
    {{--            document.addEventListener('submit', function (e) {--}}
    {{--                if (e.target.classList.contains('edit-subject-form')) {--}}
    {{--                    e.preventDefault();--}}
    {{--                    const form = e.target;--}}
    {{--                    const id = form.dataset.id;--}}
    {{--                    const url = form.dataset.url;--}}
    {{--                    const formData = new FormData(form);--}}

    {{--                    fetch(url, {--}}
    {{--                        method: 'POST',--}}
    {{--                        body: formData,--}}
    {{--                        headers: {--}}
    {{--                            'X-CSRF-TOKEN': csrfToken,--}}
    {{--                            'Accept': 'application/json',--}}
    {{--                            'X-Requested-With': 'XMLHttpRequest'--}}
    {{--                        }--}}
    {{--                    })--}}
    {{--                        .then(res => res.json())--}}
    {{--                        .then(data => {--}}
    {{--                            if (data.status === 'success') {--}}
    {{--                                const row = document.getElementById(`subjectRow${id}`);--}}
    {{--                                if (row) row.querySelector('td').textContent = data.subject.name;--}}

    {{--                                const modalEl = document.getElementById(`editSubjectModal${id}`);--}}
    {{--                                bootstrap.Modal.getInstance(modalEl)?.hide();--}}

    {{--                                showSuccessToast('Предмет обновлён');--}}
    {{--                            } else {--}}
    {{--                                showErrorToast('Ошибка при сохранении');--}}
    {{--                            }--}}
    {{--                        })--}}
    {{--                        .catch(() => showErrorToast('Ошибка при сохранении'));--}}
    {{--                }--}}
    {{--            });--}}
    {{--        });--}}
    {{--    </script>--}}
@endpushonce
