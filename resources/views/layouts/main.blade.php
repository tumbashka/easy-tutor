@extends('layouts.base')

@section('content')
    <div class="container-fluid justify-content-center custom-padding">
        <x-alert/>
        @yield('main.content')
    </div>
@endsection

