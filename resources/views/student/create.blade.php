@extends('layouts.main')

@section('title', 'Добавление ученика')

@section('main.content')
    <x-form-container>
        <form action="{{ route('student.store') }}" method="post">
            @csrf
            <x-card.card>
                <x-card.header-nav
                    :title="'Добавление ученика'"
                    :text="'Назад'"
                    :url="route('student.index')"
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








