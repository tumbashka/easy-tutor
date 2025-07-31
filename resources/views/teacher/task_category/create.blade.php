@extends('layouts.main')

@section('title', 'Добавление категории')

@section('main.content')
    <x-form-container>
        <form action="{{ route('task_categories.store') }}" method="post">
            @csrf
            <x-card.card>
                <x-card.header-nav
                    :title="'Добавление категории'"
                    :text="'Назад'"
                    :url="route('task_categories.index')"
                />
                <x-card.body>
                    <x-task.category-form/>
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








