@extends('layouts.tasks')

@section('title', 'Добавление задачи')

@section('main.content')
    <x-form-container>
        <form action="{{ route('tasks.store') }}" method="post">
            @csrf
            <x-card.card>
                <x-card.header-nav
                    :title="'Добавление задачи'"
                    :text="'Назад'"
                    :url="route('tasks.index')"
                />
                <x-card.body>
                    <x-task.form :$task_categories :$students_on_classes/>
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








