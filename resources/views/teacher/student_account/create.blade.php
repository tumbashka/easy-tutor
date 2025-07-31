@extends('layouts.main')

@section('title', "Создание профиля для ученика: {$student->name}")

@section('main.content')
    <x-form-container>
        <form action="{{ route('students.account.store', $student) }}" method="post">
            @csrf
            <x-card.card>
                <x-card.header-nav
                    :title="'Создание профиля для ученика: ' . $student->name"
                    :text="'Назад'"
                    :url="route('students.show', $student)"
                />
                <x-card.body>
                    <div class="row align-items-center">
                        <div class="col-sm-3">
                            <p class="mb-0 required-input">Имя</p>
                        </div>
                        <div class="col-sm-9">
                            <x-form.input-error-alert :name="'name'"/>
                            <input type="text" autofocus name="name" value="{{ old('name') ?? $student['name'] ?? '' }}"
                                   required
                                   class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}">
                        </div>
                    </div>
                    <hr>
                    <div class="row align-items-center">
                        <div class="col-sm-3">
                            <p class="mb-0 required-input">Email</p>
                        </div>
                        <div class="col-sm-9">
                            <x-form.input-error-alert :name="'email'"/>
                            <input type="email" name="email" value="{{ old('email') ?? '' }}"
                                   required
                                   class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}">
                        </div>
                    </div>
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








