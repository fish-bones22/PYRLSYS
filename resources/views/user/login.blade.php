@extends('layout.secondary')

@section('title')
Login
@stop

@section('style')
<link href="{{ asset('css/tile.css') }}" media="all" rel="stylesheet" type="text/css" />
@stop

@section('content')


@if (session('error') != null)
<div class="alert alert-danger">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif
@if (session('success') != null)
<div class="alert alert-success">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif


<div class="row">
    <div class="col-md-4 offset-md-4">

        <form id="loginForm" action="{{ action('UserController@login') }}" method="POST">
            @csrf
            @method('post')

            <div class="row">
                <div class="col-12 form-paper text-center">
                    <img src="{{ asset('images/logo-small.jpg') }}" class="image-responsive tile-logo"/>
                </div>
                <div class="col-12 form-paper section-divider"></div>
                <div class="col-12 form-paper section-title">Enter credentials to continue</div>
                <div class="col-12 form-paper section-divider"></div>
                <div class="col-12 form-paper">
                    <div class="form-group mt-3">
                        <input placeholder="Username" type="text" class="form-control" name="username" autocomplete="off" required />
                    </div>

                    <div class="form-group mt-3">
                        <input placeholder="Password" type="password" class="form-control" name="password" required />
                    </div>
                </div>
                <div class="col-12 form-paper">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block" form="loginForm">Login</button>
                        <div class="text-muted"><i>Ask admin for an account</i></div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


@stop
