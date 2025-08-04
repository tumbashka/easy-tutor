@extends('layouts.main')

@vite('resources/js/tom-select.js')

@section('title', 'Добавление пользователя')

@section('main.content')
    <x-form-container>
        <form action="{{ route('admin.users.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <x-card.card>
                <x-card.header-nav
                    :title="'Добавление пользователя'"
                    :text="'Назад'"
                    :url="route('admin.users.index')"
                />
                <x-card.body>
                    <x-admin.user-form :roles="$roles"/>
                </x-card.body>
                <x-card.footer>
                    <x-button type="submit">
                        Сохранить
                    </x-button>
                </x-card.footer>
            </x-card.card>
        </form>
    </x-form-container>
@endsection








