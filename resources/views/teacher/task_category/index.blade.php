@extends('layouts.main')

@section('title', 'Категории задач')

@section('main.content')
    <x-form-container>
        <x-card.card>
            <x-card.header-nav
                :title="'Категории задач'"
                :text="'Назад'"
                :url="route('tasks.index')"
            />
            <x-card.body>
                @if($task_categories->count())
                    @foreach($task_categories as $task_category)
                        <x-task.category-row :$task_category/>
                    @endforeach
                @else
                    <p class="text-center h5">
                        Категорий нет
                    </p>
                @endif
            </x-card.body>
            <x-card.footer>
                <x-link-button href="{{ route('task_categories.create') }}">
                    Добавить
                    <i class="fa-light fa-circle-plus fa-lg"></i>
                </x-link-button>
            </x-card.footer>
        </x-card.card>
    </x-form-container>
@endsection








