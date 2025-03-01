<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#000000">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="apple-touch-icon" href="/images/icons/book-192x192.png">
    <link rel="icon" href="/images/icons/book-192x192.png" type="image/png">
    <link href="/fontawesome/css/all.css" rel="stylesheet">
    @vite(['resources/sass/app.scss' , 'resources/js/app.js'])
    @stack('css')
    <title>{{ config('app.name') }} - @yield('title', config('app.name'))</title>
</head>
<body class="bg-pink">
@if(isAdminLink())
    @include('admin.parts.header')
@else
    @include('parts.header')
@endif

@yield('content')

@include('parts.footer')
@stack('js')
</body>
</html>
