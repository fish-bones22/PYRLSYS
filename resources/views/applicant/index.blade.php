@extends('layout.master')

@section('title')
Applicants
@stop

@section('content')

@if (session('error') != null)
<div class="alert alert-danger">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif
@if (session('success') != null)
<div class="alert alert-success">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif

<table class="table table-responsive">
    <thead>
        <tr>
            <th>Name</th>
            <th>Position Applying for</th>
            <th>Status</th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($applicants as $app)
        <tr>
            <td>{{ $app->fullName }}</td>
            <td>{{ key_exists('position', $app->details) ? $app->details['position']['value'] : 'No position' }}</td>
            <td>{{ key_exists('applicationstatus', $app->details) ? $app->details['applicationstatus']['value']  : 'No Status' }}</td>
            <td><a href="{{ action('ApplicantController@show', ['id' => $app->id ]) }}" class="btn btn-link">View</a></td>

            @if (key_exists('applicationstatus', $app->details) && $app->details['applicationstatus']['value'] === "Pending")
            <td><a href="{{ action('ApplicantController@process', ['id' => $app->id ]) }}" class="btn btn-link">Process</a></td>
            @elseif (key_exists('applicationstatus', $app->details) && $app->details['applicationstatus']['value'] === "Processing")
            <td><a href="{{ action('ApplicantController@hire', ['id' => $app->id ]) }}" class="btn btn-link">Hire</a></td>
            @endif

            <td>
                <form action="{{ route('applicant.destroy', $app->id) }}" method="POST">
                    @csrf
                    @method('delete')
                    <button type="submit" class="btn btn-link">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@stop
