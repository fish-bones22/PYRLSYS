@extends('layout.master')

@section('title')
Manhour Masterlist
@stop

@section('content')

<div class="row">
    <div class="col-md-8 offset-md-2 form-paper section-title">Manhour Masterlist</div>
    <div class="col-md-8 offset-md-2 form-paper section-divider"></div>
    <div class="col-md-3 offset-md-2 form-paper">
        <div class="form-group mt-3">
            <a href="{{ action('ManhourController@input', '') }}" class="btn btn-sm btn-block btn-light">Input Record</a>
        </div>
    </div>
    <div class="col-md-5 form-paper">
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label for="department" class="form-paper-label">Department</label>
                    <select class="form-control form-control-sm" id="department" onchange="filterDepartment()">
                        <option value="">All</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->value }}">{{ $dept->value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group float-right">
                    <label for="searchBox" class="form-paper-label">Search</label>
                    <input id="searchBox" type="search" class="form-control form-control-sm" onkeyup="filterEmployees()" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8 offset-md-2 form-paper section-divider"></div>
    <div class="col-md-8 offset-md-2 form-paper">
        <table class="table table-sm" id="masterListTable">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>&nbsp;</th>
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
                    <td>{{ isset($emp->current['department']['displayName']) ? $emp->current['department']['displayName'] : ''}}</td>
                    <td>
                        <span class="btn-group">
                            <a href="{{ action('ManhourController@input', ['id' => $emp->id]) }}" class="btn btn-sm btn-light">Input Record</a>
                            <a href="{{ action('ManhourController@viewRecordNow', ['id' => $emp->id ]) }}" class="btn btn-sm btn-light">View Record</a>
                        </span>
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
            <a role="button" href="{{ action('ManhourController@viewRecordCollated', date_format(now(), 'Y-m-d')) }}" class="btn btn-secondary">View Manhour Summary</a>
            <a role="button" href="{{ action('ManhourController@viewNow') }}" class="btn btn-secondary">View Daily Working Hours</a>
        </div>
    </div>
</div>

@stop
@section('script')
<script src="{{ asset('js/manhourMasterlistPage.js') }}"></script>
@stop
