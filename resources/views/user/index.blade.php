@extends('layout.master')

@section('title')
All Users
@stop

@section('content')

<a href="{{ action('UserController@addUser') }}">New User</a>
<table class="table table-responsive">
    <thead>
        <tr>
            <th>Username</th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->username }}</td>
            <td><a href="{{ action('UserController@updateUser', $user->id) }}">Edit</a></td>
            <td><a href="{{ action('UserController@deleteUser', $user->id) }}">Delete</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
@stop
