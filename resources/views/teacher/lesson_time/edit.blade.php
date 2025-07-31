@props([
    'title' => 'Изменение занятия',
])
@extends('layouts.main')

@section('title', $title)
@php
    $back = $backUrl ?? route('students.show', compact('student'));
@endphp

@section('main.content')
    <x-form-container>
        <form action="{{ route('students.lesson-times.update', compact('student', 'lesson_time', 'backUrl')) }}" method="post">
            @csrf
            @method('PUT')
            <x-card.card>
                <x-card.header-nav :title="$title" :url="$back">
                    <x-slot:text>
                        Назад
                    </x-slot:text>
                </x-card.header-nav>
                <x-card.body>
                    <x-lesson-time.form :student="$student" :lesson_time="$lesson_time" :lesson-times="$lessonTimes" />
                </x-card.body>
                <x-card.footer>
                    <x-button type="submit">
                        Сохранить
                    </x-button>
                </x-card.footer>
            </x-card.card>
        </form>
        <x-button-modal-delete
            :action="route('students.lesson-times.destroy', compact('student', 'lesson_time', 'backUrl'))"
            :text_body="'Удалить занятие?'"
        />
    </x-form-container>
@endsection








