@extends('layouts.main')

@section('title', 'Расписание')

@section('main.content')
    <x-header>
        Расписание пользователя: <a class="text-light link-underline-light link-underline-opacity-25 link-underline-opacity-75-hover"
                                    href="{{ route('user.show', $user) }}">{{ $user->name }}</a>
        <br>
        Ссылка действительна: <u>{{ $expires }}</u>
    </x-header>

    <x-free-time.week-shared
        :allLessonSlotsOnWeekDays="$allLessonSlotsOnWeekDays"
    />
@endsection








