@extends('layouts.error')

@section('title', 'Email не доступен')

@section('main.content')
    <h1 class="display-1 fw-bold">450</h1>
    <p class="fs-3"><span class="text-danger">Упс!</span> Email не доступен.</p>
    <p class="lead">Невозможно отправить сообщение на электронную почту.</p>
    <a href="{{ route('registration') }}" class="btn btn-primary">Регистрация</a>
@endsection

