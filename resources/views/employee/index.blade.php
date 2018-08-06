@extends('layout.master')

@section('title')
Employee
@stop

@section('content')

<div class="row">
    <div class="col-md-6 offset-md-3">
        <a href="employee/new">New</a>
        <table class="table table-sm">
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
