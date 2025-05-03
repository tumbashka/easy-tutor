@extends('layouts.error')

@section('title', 'Страница не найдена')

@section('main.content')
    <h1 class="display-1 fw-bold">404</h1>
    <p class="fs-3"><span class="text-danger">Упс!</span> Страница не найдена.</p>
    <p class="lead">
        Запрашиваемая страница не существует.
    </p>
    <a href="{{ route('home') }}" class="btn btn-primary">На главную</a>
@endsection


