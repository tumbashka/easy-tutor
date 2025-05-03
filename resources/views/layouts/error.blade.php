@extends('layouts.base')

@section('content')
    <div class="container-fluid justify-content-center custom-padding min-vh-100">
        <x-alert/>
        <x-form-container>
            <div class="d-flex align-items-center justify-content-center mt-5">
                <div class="text-center">
                    @yield('main.content')
                </div>
            </div>
        </x-form-container>
    </div>
@endsection

