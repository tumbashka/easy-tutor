@extends('layouts.main')

@section('title', 'Восстановление пароля')

@section('main.content')
    <x-form-container>
        <form action="{{ route('password.request') }}" method="post">
            @csrf
            <x-card.card>
                <x-card.header :title="'Восстановление пароля'"/>
                <x-error-alert/>
                <x-card.body>
                    <div class="row justify-content-center p-sm-4">
                        <x-form.input-float
                            :icon="'fas fa-at fa-lg me-3 fa-fw'"
                            :type="'email'"
                            :text="'Email'"
                            :name="'email'"/>
                        <a class="link-underline link-underline-opacity-75-hover link-underline-opacity-0 text-end" href="{{ route('login') }}">Вспомнили пароль?</a>
                    </div>
                </x-card.body>
                <x-card.footer>
                    <x-button type="submit">
                        {{ __('Отправить письмо на почту') }}
                    </x-button>
                </x-card.footer>
            </x-card.card>
        </form>
    </x-form-container>
@endsection








