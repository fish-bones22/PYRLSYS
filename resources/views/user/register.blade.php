@extends('layout.master')

@section('title')
New User
@stop

@section('content')

@if (session('error') != null)
<div class="alert alert-danger">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif
@if (session('success') != null)
<div class="alert alert-success">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif

<div class="row">
    <div class="col-md-6 offset-md-3">
        <form action="{{ action('UserController@addUser') }}" method="POST">
            @csrf
            @method('post')
            <div class="row">
                <div class="col-12 form-paper text-center"><div class="display-4">New User</div></div>
            </div>
            <div class="row">
                <div class="col-12 form-paper section-divider"></div>
                <div class="col-12 form-paper section-title">Credentials</div>
                <div class="col-12 form-paper">
                    <div class="form-group">
                        <label for="fullName" class="form-paper-label">Full Name:</label>
                        <input type="text" id="fullName" class="form-control" name="full_name" value="{{ old('full_name') }}" required />
                    </div>
                </div>
                <div class="col-12 form-paper">
                    <div class="form-group">
                        <label for="username" class="form-paper-label">Username:</label>
                        <input type="text" id="username" class="form-control" name="username"  value="{{ old('user_name') }}" required />
                    </div>
                </div>
                <div class="col-12 form-paper">
                    <div class="form-group" class="form-paper-label">
                        <label for="password" class="form-paper-label">Password:</label>
                        <input type="password" id="password" class="form-control" name="password" required />
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword" class="form-paper-label">Confirm Password:</label>
                        <input type="password" id="confirmPassword" class="form-control" name="confirm_password" required />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 form-paper section-divider"></div>
                <div class="col-12 form-paper section-title">User Access</div>

                <div class="col-12 form-paper">
                    <div class="form-check mt-2">
                        <input type="checkbox" id="cb-user-access-all" class="form-check-input" />
                        <label for="cb-user-access-all"><em>Select All</em></label>
                    </div>
                </div>
                <div class="col-12 form-paper">
                    <div class="form-check mt-2">
                        <input type="checkbox" id="isAdmin" class="form-check-input cb-user-access" name="admin" {{ old('admin') != null ? 'checked' : '' }} />
                        <label for="isAdmin">Admin</label>
                    </div>
                </div>
                <div class="col-12 form-paper">
                    <div class="form-group">
                        @for ($i = 0; $i < sizeof($roles); $i++)
                        <div class="form-check">
                            <input type="hidden" name="user_accesses[{{ $i }}][id]" value="{{ $roles[$i]->id }}" />
                            <input type="checkbox" id="{{ $roles[$i]->roleKey }}" class="form-check-input cb-user-access" name="user_accesses[{{ $i }}][key]" />
                            <label for="{{ $roles[$i]->roleKey }}">{{ $roles[$i]->roleName }}</label>
                        </div>
                        @endfor
                    </div>
                </div>

                <div class="col-12 form-paper section-divider"></div>
                <div class="col-12 form-paper section-title">Department Access</div>
                <div class="col-12 form-paper">
                    <div class="form-check mt-2">
                        <input type="checkbox" id="cb-department-access-all" class="form-check-input" />
                        <label for="cb-department-access-all"><em>Select All</em></label>
                    </div>
                </div>

                <div class="col-12 form-paper">
                    <div class="form-group">
                        @for ($i = 0; $i < sizeof($categories); $i++)
                        <div class="form-check">
                            <input type="hidden" name="department_accesses[{{ $i }}][id]" value="{{ $categories[$i]->id }}" />
                            <input type="checkbox" id="{{ $categories[$i]->value }}" class="form-check-input cb-department-access" name="department_accesses[{{ $i }}][key]" />
                            <label for="{{ $categories[$i]->value }}">{{ $categories[$i]->value }}</label>
                        </div>
                        @endfor
                    </div>
                </div>

            </div>
            <div class="mt-4 mb-4">&nbsp;</div>
            <div class="fixed-bottom btn-container m-4">
                <div class="float-right">
                    <div class="btn-group">
                        <a href="{{ action('UserController@index') }}" class="btn btn-light">Back to List</a>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary" data-confirm="register" >Register</button>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

@stop

@section('script')
<script src="{{ asset('js/registerUser.js') }}"></script>
@stop
