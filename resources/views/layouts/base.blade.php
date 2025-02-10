<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="/fontawesome/css/all.css" rel="stylesheet">
    @vite(['resources/sass/app.scss' , 'resources/js/app.js'])
    @stack('css')
    <title>@yield('title', config('app.name'))</title>
</head>
<body class="bg-pink">
@include('parts.header')

@yield('content')

@include('parts.footer')
@stack('js')

</body>
</html>
