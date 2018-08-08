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
    <nav class="navbar navbar-dark fixed-top navbar-expand-md bg-primary">
        <a href="/" class="navbar-brand">Home</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ action('UserController@index') }}">Administrator</a>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" role="button" id="humanResourceDropdown" data-toggle="dropdown">Human Resource</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ action('EmployeeController@index') }}">Employees</a>
                        <a class="dropdown-item" href="{{ action('ApplicantController@index') }}">Applicants</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" role="button" data-toggle="dropdown" data-target="#manhourDropdown">Manhour</a>
                    <div class="dropdown-menu" id="manhourDropdown">
                        <a class="dropdown-item" href="{{ action('ManhourController@index') }}">Masterlist</a>
                        <a class="dropdown-item" href="{{ action('ManhourController@input', '') }}">Input Records</a>
                        <a class="dropdown-item" href="{{ action('OtRequestController@index') }}" role="button">OT Requests</a>
                    </div>
                </li>
            </ul>
            <div class="ml-auto">
                <a href="{{ action('UserController@logout') }}" class="btn btn-outline-light float-right">Log out</a>
            </div>
        </div>
    </nav>
    <div class="m-4">&nbsp;</div>
    <div class="m-4">&nbsp;</div>
    <div class="container">
        @yield('content')
    </div>

    <script src="{{ asset('js/app.js') }}" ></script>
    @yield('script')
</body>
</html>
