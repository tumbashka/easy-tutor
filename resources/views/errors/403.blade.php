@extends('layouts.error')

@section('title', 'Доступ запрещён')

@section('main.content')
    <h1 class="display-1 fw-bold">403</h1>
    <p class="fs-3">Доступ запрещён.</p>
    <p class="lead">
        Доступ к запрашиваемому ресурсу запрещён.
    </p>
    <h2>{{ $exception->getMessage() }}</h2>
    <a href="{{ route('home') }}" class="btn btn-primary">На главную</a>
@endsection


