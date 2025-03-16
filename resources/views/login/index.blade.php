@extends('layouts.main')

@section('title', 'Вход')

@section('main.content')
    <x-form-container>
        <form action="{{ route('login.store') }}" method="post">
            @csrf
            <x-card.card>
                <x-card.header :title="'Вход'"/>
                <x-error-alert/>
                <x-card.body>
                    <div class="row justify-content-center p-sm-4">
                        <x-form.input-float
                            :icon="'fas fa-at fa-lg me-3 fa-fw'"
                            :type="'email'"
                            :text="'Email'"
                            :name="'email'"/>
                        <x-form.input-float
                            :icon="'fas fa-lock fa-lg me-3 fa-fw'"
                            :type="'password'"
                            :text="'Пароль'"
                            :name="'password'"/>
                        <x-form.checkbox class="mb-0"
                            :icon="'fas fa-regular fa-bookmark fa-lg me-3 fa-fw'"
                            :text="'Запомнить меня'"
                            :name="'remember'"
                        />
                        <div class="d-flex justify-content-end">
                            <a class="link-underline link-underline-opacity-75-hover link-underline-opacity-0 text-end" href="{{ route('password.request') }}">Забыли пароль?</a>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a class="link-underline link-underline-opacity-75-hover link-underline-opacity-0 text-end" href="{{ route('register') }}">Ещё не зарегистрированы?</a>
                        </div>
                    </div>
                </x-card.body>
                <x-card.footer>
                    <x-button type="submit">
                        {{ __('Войти') }}
                    </x-button>
                </x-card.footer>
            </x-card.card>
        </form>
    </x-form-container>
@endsection








