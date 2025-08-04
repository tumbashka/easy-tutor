@extends('layouts.main')

@vite('resources/js/tom-select.js')

@section('title', 'Доски')

@section('main.content')
    <x-form-container>
        <x-card.card>
            <x-card.header :title="'Электронные доски'"/>
            <x-card.body>
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <input type="text" name="name" value="{{ old('name')?? $student['name']?? '' }}"
                               placeholder="Название доски"
                               class="form-control form-control-sm {{ $errors->has('name') ? 'is-invalid' : '' }}">
                    </div>
                    <div class="col-sm-6">
                        <select name="student" id="student" data-tom-select-single
                                placeholder="Выберите предмет" class="w-full form-select form-select-sm"
                        >
                            <option value="">Выберите предмет</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary btn-sm d-grid">Найти</button>
                </div>
            </x-card.body>
        </x-card.card>
        <x-card.card>
            <x-card.header :title_size="">
                <x-slot:title>
                    <div class="row">
                        <div class="col">
                            @if(isset($category))
                                {{ $category->name }}
                                <a href="{{ route('tasks.index') }}" class="link-light">
                                    <i class="fa-regular fa-rectangle-xmark fa-lg"></i>
                                </a>
                            @else
                                Список
                            @endif
                        </div>
                        <div class="col-auto ms-auto">
                            <x-icon-modal-action
                                :id="'delete_completed'"
                                :action="route('tasks.delete-completed')"
                                :color="'text-white'"
                                :text_body="'Удалить выполненные задачи?'"
                                :icon="'fa-solid  fa-trash-can fa-xl'"
                            />
                        </div>
                    </div>
                </x-slot:title>
            </x-card.header>
            <x-card.body>
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <input type="text" name="name" value="{{ old('name')?? $student['name']?? '' }}"
                               placeholder="Название доски"
                               class="form-control form-control-sm {{ $errors->has('name') ? 'is-invalid' : '' }}">
                    </div>
                    <div class="col-sm-6">
                        <select name="student" id="student" data-tom-select-single
                                placeholder="Выберите предмет" class="w-full form-select form-select-sm"
                        >
                            <option value="">Выберите предмет</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary btn-sm d-grid">Найти</button>
                </div>
            </x-card.body>
        </x-card.card>
    </x-form-container>
@endsection








