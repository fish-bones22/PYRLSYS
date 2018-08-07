@extends('layout.master')

@section('title')
Update User
@stop

@section('content')

{{ session('error') }}
{{ session('success') }}

<div class="row">
    <div class="col-md-6 offset-md-3">
        <form action="{{ action('UserController@updateUser', $user->id) }}" method="POST">
            @csrf
            @method('post')

            <div class="row">
                <div class="col-12 form-paper section-title">
                    Credentials
                </div>
                <div class="col-12 form-paper">
                    <div class="form-group">
                        <label for="fullName" class="form-paper-label">Full Name:</label>
                        <input type="text" id="fullName" class="form-control" name="full_name" value="{{ $user->fullName }}" />
                    </div>
                </div>

                <div class="col-12 form-paper">
                    <div class="form-group">
                        <label for="username" class="form-paper-label">Username:</label>
                        <input type="text" id="username" class="form-control" name="username" value="{{ $user->username }}" />
                    </div>
                </div>

                <div class="col-12 form-paper">
                    <div class="form-group" id="passwordBoxToggler">
                        <button type="button" class="btn btn-light btn-block" onclick="togglePasswordBox()">Change Password</button>
                    </div>
                    <div id="passwordBox" style="display:none">
                        <button type="button" class="close" onclick="togglePasswordBox()">&times;</button>
                        <div class="form-group">
                            <label for="password" class="form-paper-label">Current Password:</label>
                            <input type="password" id="password" class="form-control" name="password" />

                            <div id="adminBox" style="display:none" >
                                <label><strong>Please enter Admin credentials</strong></label>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <input placeholder="Admin Username" type="password" id="adminUsername" class="form-control" name="admin_username" />
                                    </div>
                                    <div class="col-sm-6">
                                        <input placeholder="Admin Password" type="password" id="adminPassword" class="form-control" name="admin_password" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-check">
                                <input type="checkbox" id="forgotPassword" class="form-check-input" name="forgot_password" onchange="toggleAdminBox()" />
                                <label for="forgotPassword">Forgot Password</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="newPassword" class="form-paper-label">New Password:</label>
                            <input type="password" id="newPassword" class="form-control" name="new_password" />
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword" class="form-paper-label">Confirm New Password:</label>
                            <input type="password" id="confirmPassword" class="form-control" name="confirm_new_password" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 form-paper section-title">User Access</div>
                <div class="col-12 form-paper">
                    <div class="form-check mt-2">
                        <input type="checkbox" id="isAdmin" class="form-check-input" name="admin" {{ ($user->admin == 1) ? 'checked' : '' }} />
                        <label for="isAdmin">Admin</label>
                    </div>
                </div>
                <div class="col-12 form-paper">
                    <div class="form-group">

                        @for ($i = 0; $i < sizeof($roles); $i++)
                        <?php

                        $isChecked = '';
                        $roleId = $roles[$i]->id;

                        if ($user->accesses != null && in_array($roleId, $user->accesses))
                            $isChecked = 'checked';
                        ?>
                        <div class="form-check">
                            <input type="hidden" name="user_accesses[{{ $i }}][id]" value="{{ $roleId }}" />
                            <input type="checkbox" id="{{ $roles[$i]->roleKey }}" class="form-check-input" name="user_accesses[{{ $i }}][key]" {{ $isChecked }} />
                            <label for="{{ $roles[$i]->roleKey }}">{{ $roles[$i]->roleName }}</label>
                        </div>
                        @endfor
                    </div>

                </div>
            </div>

            <div class="fixed-bottom m-4">
                <div class="float-right">
                    <div class="btn-group">
                        <a href="{{ action('UserController@index') }}" class="btn btn-light">Back to List</a>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

@stop

@section('script')
<script src="{{ asset('js/updateUser.js') }}"></script>
@stop
