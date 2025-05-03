@extends('layouts.error')

@section('title', 'Ошибка на сервере')

@section('main.content')
    <h1 class="display-1 fw-bold">500</h1>
    <p class="fs-3">Ошибка на сервере.</p>
    <p class="lead">
        При выполнении запроса, на сервере произошла ошибка.
    </p>
    <a href="{{ route('home') }}" class="btn btn-primary">На главную</a>
@endsection


