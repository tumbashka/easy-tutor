@extends('layouts.main')

@pushonce('css')
    @vite('resources/js/tom-select.js')
@endpushonce

@section('title', 'Изменение занятия ')

@section('main.content')
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
                    <x-lesson.form :lesson="$lesson" :students="$students" :occupied-slots="$occupiedSlots" :subjects="$subjects"/>
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
