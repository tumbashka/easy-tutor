@extends('layouts.main')
@vite('resources/js/tom-select.js')
@section('title', 'Изменение занятия ')

@section('main.content')
{{--    @dump()--}}
    <x-form-container>
        <form action="{{ route('schedule.lesson.update',['day' => $day->format('Y-m-d'), 'lesson' => $lesson->id]) }}" method="post">
            @csrf
            @method('PUT')
            <x-card.card>
                <x-card.header-nav
                    :text="'Назад'"
                    :url="route('schedule.show', ['day' => $day->format('Y-m-d')])">
                    <x-slot:title>
                        Редактирование занятия {{ $day->translatedFormat('d F') }}
                    </x-slot:title>
                </x-card.header-nav>
                <x-card.body>
                    <x-lesson.form :lesson="$lesson" :students="$students"/>
                </x-card.body>
                <x-card.footer>
                    <x-button type="submit">
                        Сохранить занятие
                    </x-button>
                </x-card.footer>
            </x-card.card>
        </form>
    </x-form-container>
@endsection








