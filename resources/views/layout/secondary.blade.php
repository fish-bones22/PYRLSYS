<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('css/app.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/dev.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/form-paper.css') }}" media="all" rel="stylesheet" type="text/css" />
    @yield('style')
    <title>@yield('title')</title>
</head>
<body>
    <div class="container">
        <div class="m-3">&nbsp;</div>
        @yield('content')
    </div>
    @yield('script')
</body>
</html>
