@extends('layout.secondary')

@section('title')
Login
@stop

@section('content')

{{ session('error') }}

<div class="row">
    <div class="col-md-4 offset-md-4">

        <form id="loginForm" action="{{ action('UserController@login') }}" method="POST">
            @csrf
            @method('post')

            <div class="row">
                <div class="col-12 form-paper text-center">
                    <div class="display-4">Login</div>
                </div>
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
