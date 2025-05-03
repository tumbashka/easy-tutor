@extends('layouts.error')

@section('title', 'Действие не авторизовано')

@section('main.content')
    <h1 class="display-1 fw-bold">401</h1>
    <p class="fs-3">Действие не авторизовано.</p>
    <h2>{{ $exception->getMessage() }}</h2>
    <a href="{{ route('home') }}" class="btn btn-primary">На главную</a>
@endsection


