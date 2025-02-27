@extends('layouts.main')

@section('title', 'Просмотр задачи')

@section('main.content')
    <x-form-container>
        <x-card.card>
            <x-card.header-nav :title="'Просмотр задачи'" :url="route('tasks.index')">
                <x-slot:text>
                    Назад
                    <i class="fa-light fa-arrow-left-from-bracket fa-lg"></i>
                </x-slot:text>
            </x-card.header-nav>
            <x-card.body>
                <x-task.profile :$task/>
            </x-card.body>
            <x-card.footer>
                <x-link-button href="{{ route('tasks.edit', $task) }}">
                    Редактировать
                    <i class="fa-light fa-pen-to-square fa-lg"></i>
                </x-link-button>
            </x-card.footer>
        </x-card.card>
        <x-link-button-on-red :href="route('tasks.change-completed', $task)">
            Отметить как выполненную
        </x-link-button-on-red>
        <x-button-modal-delete :text_body="'Удалить задачу?'" :action="route('tasks.destroy', $task)"/>
    </x-form-container>
@endsection








