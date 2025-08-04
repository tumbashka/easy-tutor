@extends('layouts.main')

@section('title', 'Настройки')

@section('main.content')
    <x-form-container>
        <!-- Список предметов -->
        <x-card>
            <x-card.header :title="'Список предметов'"/>
            <x-card.body>
                @forelse($subjects as $subject)
                    <div class="row">
                        <div class="col">
                            {{ $subject->name }}
                            @if($subject->pivot->is_default)
                                <span
                                    class="badge rounded-pill text-bg-success fw-normal text-white">По умолчанию</span>
                            @endif
                        </div>
                        <div class="col-sm-4">
                            @if(!$subject->pivot->is_default)
                                <x-link-button :href="route('user.settings.subjects.default', $subject)"
                                               :color="'primary'"
                                               class="btn-sm text-light">
                                    Сделать по умолчанию
                                </x-link-button>
                            @endif
                        </div>
                    </div>
                    @if(!$loop->last)
                        <hr class="my-2">
                    @endif
                @empty
                    <h5 class="text-center">Предметы еще не выбраны</h5>
                @endforelse

            </x-card.body>
            <x-card.footer>
                <x-link-button :href="route('user.settings.subjects.index')">Все доступные предметы</x-link-button>
            </x-card.footer>
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
