@extends('layout.master')

@section('title')
All Users
@stop

@section('content')

<div class="row">
    <div class="col-sm-4 offset-sm-4 form-paper section-title">Users</div>
    <div class="col-sm-4 offset-sm-4 form-paper section-divider"></div>
    <div class="col-sm-2 offset-sm-4 form-paper">
        <div class="form-group mt-3">
            <a href="{{ action('UserController@addUser') }}" role="button" class="btn btn-sm btn-block btn-light">Add User</a>
        </div>
    </div>
    <div class="col-sm-2 form-paper">
        <div class="form-group">
            <label for="searchBox" class="form-paper-label">Search</label>
            <input id="searchBox" type="search" class="form-control form-control-sm" onkeyup="filterUsers()" />
        </div>
    </div>
    <div class="col-sm-4 offset-sm-4 form-paper section-divider"></div>
    <div class="col-sm-4 offset-sm-4 form-paper">
        <table class="table table-sm" id="usersTable">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->username }}</td>
                    <td>
                        <span class="btn-group">
                            <a role="button" class="btn btn-sm btn-light" href="{{ action('UserController@updateUser', $user->id) }}">Edit</a>
                            <form action="{{ route('user.delete', $user->id) }}" method="POST">
                                @csrf
                                @method('post')
                                <input type="submit" class="btn btn-sm btn-light" value="Delete" />
                            </form>
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop

@section('script')
<script src="{{ asset('js/usersPage.js') }}"></script>
@stop
