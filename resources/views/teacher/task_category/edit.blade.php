@extends('layouts.main')

@section('title', 'Редактирование категории')

@section('main.content')
    <x-form-container>
        <form action="{{ route('task_categories.update', $task_category) }}" method="post">
            @csrf
            @method('PUT')
            <x-card.card>
                <x-card.header-nav
                    :title="'Редактирование категории'"
                    :text="'Назад'"
                    :url="route('task_categories.index')"
                />
                <x-card.body>
                    <x-task.category-form :$task_category/>
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








