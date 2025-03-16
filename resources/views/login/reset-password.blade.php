@extends('layouts.main')

@section('title', 'Сброс пароля')

@section('main.content')
    <x-form-container>
        <form action="{{ route('password.update') }}" method="post">
            @csrf
            <input type="hidden" name="token" value="{{ request()->route('token') }}">
            <x-card.card>
                <x-card.header :title="'Сброс пароля'"/>
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
                            :text="'Новый пароль'"
                            :name="'password'"/>
                        <x-form.input-float
                            :icon="'fas fa-key fa-lg me-3 fa-fw'"
                            :type="'password'"
                            :text="'Подтвердите пароль'"
                            :name="'password_confirmation'"/>
                    </div>
                </x-card.body>
                <x-card.footer>
                    <x-button type="submit">
                        {{ __('Изменить пароль') }}
                    </x-button>
                </x-card.footer>
            </x-card.card>
        </form>
    </x-form-container>
@endsection








