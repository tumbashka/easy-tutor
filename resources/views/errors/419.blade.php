@extends('layouts.error')

@section('title', 'Страница устарела')

@section('main.content')
    <h1 class="display-1 fw-bold">419</h1>
    <p class="fs-3">Страница устарела.</p>
    <p class="lead">
        Обновите страницу.
    </p>
    <a href="{{ route('home') }}" class="btn btn-primary">На главную</a>
@endsection


