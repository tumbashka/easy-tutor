@props([
    'title' => 'Редактирование домашней работы',
])
@extends('layouts.main')

@section('title', $title)

@section('main.content')
    <x-form-container>
        <form action="{{ route('students.homeworks.update', compact('student', 'homework')) }}" method="post">
            @csrf
            @method('PUT')
            <x-card.card>
                <x-card.header-nav :title="$title" :url="route('students.show', $student)">
                    <x-slot:text>
                        Назад
                    </x-slot:text>
                </x-card.header-nav>
                <x-card.body>
                    <x-homework.form :$homework />
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








