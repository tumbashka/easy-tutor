@props([
    'title' => 'Изменение занятия',
])
@extends('layouts.main')

@section('title', $title)
@php
    $back = $backUrl ?? url()->previous();
@endphp

@section('main.content')
    <x-form-container>
        <form action="{{ route('student.lesson-time.update', compact('student', 'lessonTime', 'backUrl')) }}" method="post">
            @csrf
            @method('PUT')
            <x-card.card>
                <x-card.header-nav :title="$title" :url="$back">
                    <x-slot:text>
                        Назад
                    </x-slot:text>
                </x-card.header-nav>
                <x-card.body>
                    <x-lesson-time.form :student="$student" :lesson-time="$lessonTime" />
                </x-card.body>
                <x-card.footer>
                    <x-button type="submit">
                        Сохранить
                    </x-button>
                </x-card.footer>
            </x-card.card>
        </form>
        <x-button-modal-delete
            :action="route('student.lesson-time.delete', compact('student', 'lessonTime', 'backUrl'))"
            :text_body="'Удалить занятие?'"
        />
    </x-form-container>
@endsection








