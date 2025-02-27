@extends('layouts.main')

@section('title', 'Редактирование пользователя')

@section('main.content')
    <x-form-container>
        <form action="{{ route('admin.users.update', $user) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <x-card.card>
                <x-card.header-nav
                    :title="'Редактирование пользователя'"
                    :text="'Назад'"
                    :url="route('admin.users.index')"
                />
                <x-card.body>
                    <x-admin.user-form :$user/>
                </x-card.body>
                <x-card.footer>
                    <x-button type="submit">
                        Сохранить
                    </x-button>
                </x-card.footer>
            </x-card.card>
        </form>
        <x-button-modal-delete :text_body="'Удалить пользователя?'" :action="route('admin.users.destroy', $user)"/>
    </x-form-container>
@endsection








