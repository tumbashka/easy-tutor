@extends('layouts.error')

@section('title', 'Требуется оплата')

@section('main.content')
    <h1 class="display-1 fw-bold">402</h1>
    <p class="fs-3">Требуется оплата.</p>
    <h2>{{ $exception->getMessage() }}</h2>
    <a href="{{ route('home') }}" class="btn btn-primary">На главную</a>
@endsection
