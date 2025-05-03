@extends('layouts.error')

@section('title', 'Ресурс недоступен')

@section('main.content')
    <h1 class="display-1 fw-bold">410</h1>
    <p class="fs-3">Ресурс недоступен.</p>
    <p class="lead">
        Запрашиваемый ресурс был удалён.
    </p>
    <a href="{{ route('home') }}" class="btn btn-primary">На главную</a>
@endsection


