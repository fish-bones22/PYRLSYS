@extends('layout.master')

@section('title')
Employee
@stop

@section('content')

<a href="employee/new">New</a>
<table class="table table-responsive">
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
            <td>{{ $emp->employeeId }}</td>
            <td>{{ $emp->fullName }}</td>
            <td><a href="{{ action('EmployeeController@show', ['id' => $emp->id ]) }}">Edit</a></td>
            <td><a href="{{ action('EmployeeController@destroy', ['id' => $emp->id ]) }}">Delete</a></td>
        </tr>
        @endforeach
    </tbody>
</table>

@stop
