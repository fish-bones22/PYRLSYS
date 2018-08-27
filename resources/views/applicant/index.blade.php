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


<div class="row">
    <div class="col-md-8 offset-md-2 form-paper section-title">Applicants</div>
    <div class="col-md-8 offset-md-2 form-paper section-divider"></div>
    <div class="col-md-2 offset-md-2 form-paper">
        <div class="form-group mt-3">
            <a href="{{ action('ApplicantController@new') }}" role="button" class="btn btn-sm btn-block btn-light">Application Form</a>
        </div>
    </div>
    <div class="col-md-3 form-paper">
        <div class="form-group">
            <label for="status" class="form-paper-label">Filter Applicant Status</label>
            <select id="status" class="form-control form-control-sm" onchange="filterStatus()">
                <option value="">All</option>
                <option value="Pending">Pending</option>
                <option value="Processing">Processing</option>
                <option value="Hired">Hired</option>
            </select>
        </div>
    </div>
    <div class="col-md-3 form-paper">
        <div class="form-group">
            <label for="searchBox" class="form-paper-label">Search</label>
            <input id="searchBox" type="search" class="form-control form-control-sm" onkeyup="searchApplicants()" />
        </div>
    </div>
    <div class="col-md-8 offset-md-2 form-paper section-divider"></div>
    <div class="col-md-8 offset-md-2 form-paper" >

        <table class="table table-sm table-responsive-sm mt-4" id="dataTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Applying for</th>
                    <th>Date Applied</th>
                    <th>Status</th>
                    <th>Action</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @foreach($applicants as $app)
                <tr>
                    <td><a href="{{ action('ApplicantController@show', ['id' => $app->id ]) }}" class="btn btn-link">{{ $app->fullName }}</a></td>
                    <td>{{ key_exists('position', $app->details) ? $app->details['position']['value'] : 'No position' }}</td>
                    <td>{{ date_format($app->dateUpdated, 'M j, y') }}</td>
                    <td>{{ key_exists('applicationstatus', $app->details) ? $app->details['applicationstatus']['value']  : 'No Status' }}</td>

                    <td>

                        @if (key_exists('applicationstatus', $app->details) && $app->details['applicationstatus']['value'] === "Pending")

                        <a href="{{ action('ApplicantController@process', ['id' => $app->id ]) }}" class="btn btn-sm btn-secondary">Process</a>

                        @elseif (key_exists('applicationstatus', $app->details) && $app->details['applicationstatus']['value'] === "Processing")

                        <a href="{{ action('ApplicantController@hire', ['id' => $app->id ]) }}" class="btn btn-sm btn-secondary">Hire</a>

                        @else
                        <a href="{{ action('EmployeeController@show', ['id' => $app->id ]) }}" class="btn btn-sm btn-secondary">Assign</a>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('applicant.destroy', $app->id) }}" method="POST">
                            @csrf
                            @method('delete')
                            <button type="submit" class="close" data-confirm="delete">&times;</button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>

<div class="m-4">&nbsp;</div>
<div class="fixed-bottom btn-container m-4">
    <div class="float-right">
        <div class="btn-group">
            <form action="{{ route('applicant.deleteall') }}" method="POST">
                @csrf
                @method('post')
                <input type="submit" class="btn btn-primary" data-confirm="delete all" value="Delete All"/>
            </form>
        </div>
    </div>
</div>

@stop
@section('script')
<script src="{{ asset('js/applicantsPage.js') }}"></script>
@stop
