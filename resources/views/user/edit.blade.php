@extends('layouts.main')

@section('title', 'Редактирование профиля')

@section('main.content')
    <x-form-container>
        <x-card.card>
            <form action="{{ route('user.update', $user) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <x-card.header :title="'Редактирование профиля'"/>
                <x-card.body>
                    <x-user.edit-form :$user/>
                </x-card.body>
                <x-card.footer>
                    <x-button type="submit">
                        Сохранить
                    </x-button>
                </x-card.footer>
            </form>
        </x-card.card>
    </x-form-container>
@endsection








