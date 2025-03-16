@extends('layouts.main')

@section('title', 'Ресурс недоступен')

@section('main.content')
    <x-form-container>
        <div class="d-flex align-items-center justify-content-center mt-5">
            <div class="text-center">
                <h1 class="display-1 fw-bold">429</h1>
                <p class="fs-3">Слишком много запросов.</p>
                <p class="lead">
                    Попробуйте позже.
                </p>
            </div>
        </div>
    </x-form-container>
@endsection


