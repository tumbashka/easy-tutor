@extends('layouts.base')

@section('content')
    <div class="container justify-content-center">
        <x-alert/>
        @yield('main.content')
    </div>
@endsection

