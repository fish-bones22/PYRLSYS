@extends('layout.master')

@section('title')
Manhour
@stop

@section('content')

<div class="row">
    <div class="col-md-6 offset-md-3">
        <a href="{{ action('ManhourController@input', '') }}" class="btn btn-link">Input Record</a>
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
                <?php
                $index = 0;
                ?>
                @foreach($employees as $emp)
                <?php
                    $next = 0;
                    if ($index == sizeof($employees) - 1)
                        $next = null;
                    else
                        $next = $employees[$index+1]->id;

                    $index++;
                ?>
                <tr>
                    <td>{!! $emp->employeeId != null ? $emp->employeeId : '<i class="small text-muted">No ID</i>' !!}</td>
                    <td>{{ $emp->fullName }}</td>
                    <td><a href="{{ action('ManhourController@input', ['id' => $emp->id]) }}">Input Record</a></td>
                    <td><a href="{{ action('EmployeeController@show', ['id' => $emp->id ]) }}">View Record</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@stop
