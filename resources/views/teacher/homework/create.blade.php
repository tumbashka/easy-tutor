@props([
    'title' => 'Добавление домашней работы',
])
@extends('layouts.main')

@section('title', $title)

@section('main.content')
    <x-form-container>
        <form action="{{ route('students.homeworks.store', $student) }}" method="post">
            @csrf
            <x-card.card>
                <x-card.header-nav :title="$title" :url="url()->previous()">
                    <x-slot:text>
                        Назад
                    </x-slot:text>
                </x-card.header-nav>
                <x-card.body>
                    <x-homework.form/>
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








