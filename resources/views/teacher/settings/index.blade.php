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
