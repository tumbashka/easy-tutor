@extends('layouts.base')

@section('content')
    <div class="container-fluid justify-content-center custom-padding min-vh-100">
        <x-alert/>
        @yield('main.content')
    </div>
@endsection

