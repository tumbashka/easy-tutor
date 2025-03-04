@extends('layouts.main')

@section('title', 'Профиль педагога')

@section('main.content')
    <x-form-container>
        <x-card.card>
            <x-card.header :title="'Профиль педагога'"/>
            <x-card.body>
                <x-user.profile-body :$user/>
            </x-card.body>
            <x-card.footer>
                @if(auth()->id() && auth()->user()->id == $user->id)
                    <x-link-button href="{{ route('user.edit', $user) }}">
                        Редактировать
                        <i class="fa-light fa-pen-to-square fa-lg"></i>
                    </x-link-button>
                @endif
            </x-card.footer>
        </x-card.card>
        @can('active-account')
            @if(auth()->user()->id == $user->id)
                <x-card.card>
                    <x-card.header :title="'Привязка Telegram'"/>
                    <x-card.body>
                        @if($user->telegram_chat_id)
                            <h6>Telegram аккаунт:{{ $user->telegram_username }} успешно привязан.</h6>
                            <p>Для смены телеграмм аккаунта, отправьте команду, располагающуюся ниже, <a href="{{ $telegram_bot_url }}">боту</a>, либо перейдите по ссылке.</p>
                        @else
                            <h6>Telegram аккаунт не привязан к профилю.</h6>
                            <p>Для привязки телеграмм аккаунта, отправьте команду, располагающуюся ниже, <a href="{{ $telegram_bot_url }}">боту</a>, либо перейдите по ссылке.</p>
                        @endif
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="encrypted_url"
                                   value="{{ '/start '.$user->telegram_token }}" readonly>
                            <button type="button" class="btn btn-outline-info btn-copy" data-bs-toggle="tooltip"
                                    data-clipboard-target="#encrypted_url">
                                <i class="far fa-copy"></i>
                            </button>
                        </div>
                    </x-card.body>
                    <x-card.footer>

                        <x-link-button href="{{ $telegram_connect_url }}">
                            Подключить
                        </x-link-button>

                    </x-card.footer>
                </x-card.card>
            @endif
        @endcan
    </x-form-container>
@endsection








