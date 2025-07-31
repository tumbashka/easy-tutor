@extends('layouts.main')
@vite('resources/js/tom-select.js')
@section('title', 'Добавление занятия')

@section('main.content')
    <x-form-container>
    <form action="{{ route('schedule.lesson.store',['day' => $day->format('Y-m-d')]) }}" method="post">
            @csrf
            <x-card.card>
                <x-card.header-nav
                    :text="'Назад'"
                    :url="route('schedule.show', ['day' => $day->format('Y-m-d')])">
                    <x-slot:title>
                        Новое занятие {{ getShortDayName($day) }}. {{ $day->translatedFormat('d F') }}
                    </x-slot:title>
                </x-card.header-nav>
                <x-card.body>
                    <x-lesson.form :students="$students" :occupied-slots="$occupiedSlots"/>
                </x-card.body>
                <x-card.footer>
                    <x-button type="submit">
                        Добавить занятие
                    </x-button>
                </x-card.footer>
            </x-card.card>
        </form>
    </x-form-container>
@endsection
