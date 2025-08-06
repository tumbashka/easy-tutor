@extends('layouts.main')

@vite('resources/js/tom-select.js')

@section('title', 'Регистрация')

@section('main.content')
    <x-form-container>
        <form action="{{ route('register.store') }}" method="post">
            @csrf
            <x-card.card>
                <x-card.header :title="'Регистрация'"/>
                <x-card.body>
                    <div class="row  p-sm-4">
                        <i class=""></i>
                        <x-form.select-float
                            :icon="'fas fa-user-tag fa-lg me-3 fa-fw'"
                            :text="'Выберите роль'"
                            :name="'role'"
                            :values="$roles['values']"
                            :names="$roles['names']"
                        />
                        <x-form.input-float
                            :icon="'fas fa-id-card fa-lg me-3 fa-fw'"
                            :text="'Имя'"
                            :name="'name'"/>
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
                        <x-form.input-float
                            :icon="'fas fa-key fa-lg me-3 fa-fw'"
                            :type="'password'"
                            :text="'Подтвердите пароль'"
                            :name="'password_confirmation'"/>
                        <div class="d-flex justify-content-end">
                            <a class="link-underline link-underline-opacity-75-hover link-underline-opacity-0 text-end"
                               href="{{ route('login') }}">Уже зарегистрированы?</a>
                        </div>
                    </div>
                </x-card.body>
                <x-card.footer>
                    <x-button type="submit">
                        {{ __('Зарегистрироваться') }}
                    </x-button>
                </x-card.footer>
            </x-card.card>
        </form>
    </x-form-container>
@endsection








