@extends('layouts.main')

@section('title', 'Вход')

@section('main.content')
    <x-form-container>
        <div class="d-flex align-items-center justify-content-center mt-5">
            <div class="text-center">
                <h1 class="display-1 fw-bold">450</h1>
                <p class="fs-3"><span class="text-danger">Упс!</span> Email не доступен.</p>
                <p class="lead">Невозможно отправить сообщение на электронную почту.</p>
                <a href="{{ route('registration') }}" class="btn btn-primary">Регистрация</a>
            </div>
        </div>
    </x-form-container>
@endsection

