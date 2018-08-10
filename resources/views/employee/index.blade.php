@extends('layout.master')

@section('title')
Employee
@stop

@section('content')

<div class="row">
    <div class="col-md-6 offset-md-3 form-paper section-title">Employees</div>
    <div class="col-md-6 offset-md-3 form-paper section-divider"></div>
    <div class="col-md-2 offset-md-3 form-paper">
        <div class="form-group mt-3">
            <a role="button" href="employee/new" class="btn btn-sm btn-block btn-light">New Employee</a>
        </div>
    </div>
    <div class="col-md-4 form-paper">
        <div class="form-group">
            <label for="searchBox" class="form-paper-label">Search</label>
            <input id="searchBox" class="form-control form-control-sm" type="search" onkeyup="filterEmployees()" />
        </div>
    </div>
    <div class="col-md-6 offset-md-3 form-paper section-divider"></div>
    <div class="col-md-6 offset-md-3 form-paper">
        <table class="table table-sm" id="employeesTable">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $emp)
                <tr>
                    <td>{!! $emp->employeeId != null ? $emp->employeeId : '<i class="small text-muted">No ID</i>' !!}</td>
                    <td>{{ $emp->fullName }}</td>
                    <td><a href="{{ action('EmployeeController@show', ['id' => $emp->id ]) }}">Edit</a></td>
                    <td><a href="{{ action('EmployeeController@destroy', ['id' => $emp->id ]) }}">Delete</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@stop
@section('script')
<script src="{{ asset('js/employeesPage.js') }}"></script>
@stop

