@extends('layout.master')

@section('title')
All Users
@stop

@section('content')

<div class="row">
    <div class="col-sm-4 offset-sm-4">

        <a href="{{ action('UserController@addUser') }}">New User</a>
        <table class="table table-sm">
            <thead>
                <tr class="form-paper">
                    <th>Username</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="">
                    <td class="form-paper">{{ $user->username }}</td>
                    <td class="form-paper"><a href="{{ action('UserController@updateUser', $user->id) }}">Edit</a></td>
                    <td class="form-paper"><a href="{{ action('UserController@deleteUser', $user->id) }}">Delete</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop
