@extends('layouts.main')

@section('title', 'Страница не найдена')

@section('main.content')
    <x-form-container>
        <div class="d-flex align-items-center justify-content-center mt-5">
            <div class="text-center">
                <h1 class="display-1 fw-bold">410</h1>
                <p class="fs-3">Ресурс недоступен.</p>
                <p class="lead">
                    Запрашиваемый ресурс был удалён.
                </p>
                <a href="{{ route('home') }}" class="btn btn-primary">На главную</a>
            </div>
        </div>
    </x-form-container>
@endsection


