@extends('layout.master')

@section('title')
New User
@stop

@section('content')

<div class="row">
    <div class="col-md-6 offset-md-3">
        <form action="{{ action('UserController@addUser') }}" method="POST">
            @csrf
            @method('post')
            <div class="form-group">
                <label for="fullName">Full Name:</label>
                <input type="text" id="fullName" class="form-control" name="full_name" />
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" class="form-control" name="username" />
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" class="form-control" name="password" />
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm Password:</label>
                <input type="password" id="confirmPassword" class="form-control" name="confirm_password" />
            </div>
            <div class="form-check">
                <input type="checkbox" id="isAdmin" class="form-check-input" name="admin" />
                <label for="isAdmin">Admin</label>
            </div>

            <div class="form-group">

                @for ($i = 0; $i < sizeof($roles); $i++)
                <div class="form-check">
                    <input type="hidden" name="user_accesses[{{ $i }}][id]" value="{{ $roles[$i]->id }}" />
                    <input type="checkbox" id="{{ $roles[$i]->roleKey }}" class="form-check-input" name="user_accesses[{{ $i }}][key]" />
                    <label for="{{ $roles[$i]->roleKey }}">{{ $roles[$i]->roleName }}</label>
                </div>
                @endfor
            </div>

            <div class="fixed-bottom m-4">
                <div class="float-right">
                    <div class="btn-group">
                        <a href="{{ action('UserController@index') }}" class="btn btn-light">Back to List</a>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

@stop
