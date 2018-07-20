@extends('layout.secondary')

@section('title')
Login
@stop

@section('content')

{{ session('error') }}

<div class="row">
    <div class="col-md-4 offset-md-4">
        <div class="card">
            <div class="card-content">
                <div class="card-header">
                    <div class="display-4">Login</div>
                </div>
                <div class="card-body">
                    <form id="loginForm" action="{{ action('UserController@login') }}" method="POST">
                        @csrf
                        @method('post')
                        <div class="form-group">
                            <input placeholder="Username" type="text" class="form-control" name="username" required />
                        </div>
                        <div class="form-group">
                            <input placeholder="Password" type="password" class="form-control" name="password" required />
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block" form="loginForm">Login</button>
                        <div class="text-muted"><i>Ask admin for an account</i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@stop
