@extends('layouts.main')

@section('title', 'Окна')

@section('main.content')
    <x-form-container>
        <x-card.card>
            <x-card.header-nav :title="'Настройка свободного времени'" :url="route('home')">
                <x-slot:text>
                    На главную
                    <i class="fa-regular fa-solid fa-house "></i>
                </x-slot:text>
            </x-card.header-nav>
            <x-card.body>
                <x-free-time.body :encrypted_url="$encrypted_url"/>
            </x-card.body>
            <x-card.footer>
                <x-link-button :href="route('free-time.create')">
                    Добавить окно
                    <i class="fa-light fa-circle-plus fa-lg"></i>
                </x-link-button>
            </x-card.footer>
        </x-card.card>
    </x-form-container>
    <x-free-time.week :all_lesson_slots_on_days="$all_lesson_slots_on_days" :week_days="$week_days"/>
@endsection








