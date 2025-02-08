@extends('layouts.main')

@section('title', 'Вход')

@section('main.content')
    <x-form-container>
        <x-card.card>
            <x-card.header
                :title="'Подтверждение аккаунта'"
            />
            <x-card.body>
                    <h3 class="h5">
                        Спасибо за регистрацию в нашем сервисе для репетиторов!
                    </h3>
                    <hr>
                    <p>
                        На почту <b>{{ request()->user()->email }}</b> отправлено письмо с ссылкой для подтверждения
                        аккаунта.
                    </p>
                    @if (session('status') == 'verification-link-sent')
                        <p>
                            Новое письмо с ссылкой для подтверждения аккаунта отправлено на почтовый адрес, указанный
                            при
                            регистрации.
                        </p>
                    @endif
            </x-card.body>
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <x-card.footer>
                    <x-button type="submit">
                        Отправить новое письмо
                    </x-button>
                </x-card.footer>
            </form>
        </x-card.card>
    </x-form-container>
@endsection








