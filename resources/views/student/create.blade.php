@extends('layouts.main')

@section('title', 'Добавление ученика')

@php
$back = $free_time ? route('free-time.set-student', compact('free_time')) : route('student.index');
@endphp

@section('main.content')
    <x-form-container>
        <form action="{{ route('student.store', compact('free_time')) }}" method="post">
            @csrf
            <x-card.card>
                <x-card.header-nav
                    :title="'Добавление ученика'"
                    :text="'Назад'"
                    :url="$back"
                />
                <x-card.body>
                    <x-student.form/>
                </x-card.body>
                <x-card.footer>
                    <x-button type="submit">
                        Сохранить
                    </x-button>
                </x-card.footer>
            </x-card.card>
        </form>
    </x-form-container>
@endsection








