@props([
    'title' => 'Добавление занятия',
])
@extends('layouts.main')

@section('title', $title)

@section('main.content')
    <x-form-container>
        <form action="{{ route('students.lesson-times.store', $student) }}" method="post">
            @csrf
            <x-card.card>
                <x-card.header-nav :title="$title" :url="url()->previous()">
                    <x-slot:text>
                        Назад
                    </x-slot:text>
                </x-card.header-nav>
                <x-card.body>
                    <x-lesson-time.form/>
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








