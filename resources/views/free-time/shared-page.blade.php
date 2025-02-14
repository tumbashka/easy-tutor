@extends('layouts.main')

@section('title', 'Расписание')

@section('main.content')
    <x-header>
        Расписание пользователя: {{ $user->name }}
    </x-header>

    <x-free-time.week-shared
        :all_lesson_slots_on_days="$all_lesson_slots_on_days"
    />
@endsection








