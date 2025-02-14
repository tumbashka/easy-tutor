@extends('layouts.main')

@section('title', 'Добавление окна')

@section('main.content')
    <x-form-container>
        <form action="{{ route('free-time.store') }}" method="post">
            @csrf
            <x-card.card>
                <x-card.header-nav
                    :text="'Назад'"
                    :url="route('free-time.index')">
                    <x-slot:title>
                        Добавление окна
                    </x-slot:title>
                </x-card.header-nav>
                <x-card.body>
                    <x-free-time.form-create :day="$day"/>
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








