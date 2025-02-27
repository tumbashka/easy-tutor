@extends('layouts.base')

@pushonce('css')
    @vite('resources/js/chartjs.js')
    @vite('resources/js/flatpickr.js')
@endpushonce

@section('content')
    <div class="container-fluid justify-content-center custom-padding">
        <x-alert/>
        @yield('main.content')
    </div>
@endsection

