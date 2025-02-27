@extends('layouts.main')

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
                    <x-admin.user-form/>
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








