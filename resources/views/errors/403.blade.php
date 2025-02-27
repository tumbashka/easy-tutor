@extends('layouts.main')

@section('title', 'Доступ запрещён')

@section('main.content')
    <x-form-container>
        <div class="d-flex align-items-center justify-content-center mt-5">
            <div class="text-center">
                <h1 class="display-1 fw-bold">403</h1>
                <p class="fs-3">Доступ запрещён.</p>
                <p class="lead">
                    Доступ к запрашиваемому ресурсу запрещён.
                </p>
                <h2>{{ $exception->getMessage() }}</h2>
                <a href="{{ route('home') }}" class="btn btn-primary">На главную</a>
            </div>
        </div>
    </x-form-container>
@endsection


