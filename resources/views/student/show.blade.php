@props([
    'title' => "Ученик: {$student->name}",
])
@extends('layouts.main')

@section('title', $title)

@section('main.content')
    <x-form-container>

        <x-card.card>
            <x-card.header-nav :title="$title" :url="route('student.index')">
                <x-slot:text>
                    Назад
                    <i class="fa-light fa-arrow-left-from-bracket fa-lg"></i>
                </x-slot:text>
            </x-card.header-nav>
            <x-card.body>
                <x-student.profile :student="$student"/>
            </x-card.body>
            <x-card.footer>
                <x-link-button href="{{ route('student.edit', $student) }}">
                    Редактировать
                    <i class="fa-light fa-pen-to-square fa-lg"></i>
                </x-link-button>
            </x-card.footer>
        </x-card.card>
        <x-card.card>
            <x-card.header
                :title="'Занятия'"
            />
            <x-card.body>
                @if(!$lesson_times->count())
                    <p class="text-center h5">
                        Список занятий пуст
                    </p>
                @else
                    @foreach($lesson_times as $lesson_time)
                        <x-student.lesson :lesson_time="$lesson_time"/>
                    @endforeach
                @endif
            </x-card.body>
            <x-card.footer>
                <x-link-button href="{{ route('student.lesson-time.create', $student) }}">
                    Добавить
                    <i class="fa-light fa-circle-plus fa-lg"></i>
                </x-link-button>
            </x-card.footer>
        </x-card.card>
    </x-form-container>
@endsection








