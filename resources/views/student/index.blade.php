@extends('layouts.main')

@section('title', 'Список учеников')

@section('main.content')
    <x-header-two-nav
        :title="'Список учеников'"
        :left_url="route('schedule.index')"
        :right_url="route('students.create')">
        <x-slot:left_text>
            На главную
            <i class="fa-light fa-home fa-lg"></i>
        </x-slot:left_text>
        <x-slot:right_text>
            Добавить
            <i class="fa-light fa-circle-plus fa-lg"></i>
        </x-slot:right_text>
    </x-header-two-nav>
    <div class="row justify-content-center">
        @if($students->isEmpty())
            <h3 class="text-center mt-5">Список учеников пуст.</h3>
        @else
            @foreach($students as $class => $students)
                <x-student.class-table :class="$class" :students="$students"/>
            @endforeach
        @endif
    </div>
@endsection








