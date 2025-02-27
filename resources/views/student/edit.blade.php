@extends('layouts.main')

@section('title', 'Редактирование ученика')

@section('main.content')
    <x-form-container>
        <x-card.card>
            <x-card.header-nav
                :title="'Редактирование ученика'"
                :text="'Назад'"
                :url="route('students.show', $student)"
            />
            <form action="{{ route('students.update',  $student['id']) }}" method="post">
                @method('PUT')
                @csrf
                <x-card.body>
                    <x-student.form
                        :student="$student"
                    />
                </x-card.body>
                <x-card.footer>
                    <x-button type="submit">
                        Сохранить
                    </x-button>
                </x-card.footer>
            </form>
        </x-card.card>
        <x-button-modal-delete :action="route('students.destroy', $student['id'])"/>
    </x-form-container>
@endsection








