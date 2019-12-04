@extends('layout.master')

<?php
$title = 'View Attendance';
?>

@section('title')
{{ $title }}
@stop

@section('content')

@if (session('error') != null)
<div class="alert alert-danger">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif
@if (session('success') != null)
<div class="alert alert-success">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif

<div class="row">
    <div class="col-12 form-paper section-title" id="title">{{ $title }}</div>
    <div class="col-12 form-paper section-divider"></div>
</div>
<form action="{{ route('manhour.attendance') }}" method="GET">
    @csrf
    @method('get')
    <div class="row">
        <input name="ispostback" type="hidden" />
        <div class="col-md-3 form-paper">
            <div class="form-group">
                <label for="department" class="form-paper-label">Employee ID</label>
                <input name="employeeid" class="form-control form-control-sm" id="employeeId" value="{{ isset($details['employeeid']) ? $details['employeeid'] : old('employeeid') }}" />
            </div>
        </div>
        <div class="col-md-3 col-6 form-paper">
            <div class="form-group">
                <label for="dateFrom" class="form-paper-label">From</label>
                <input name="datefrom" type="date" class="form-control form-control-sm" id="dateFrom" value="{{ isset($details['datefrom']) ? $details['datefrom'] : (old('datefrom') != null ? old('datefrom') : date_format(now(), 'Y-m-d')) }}" />
            </div>
        </div>
        <div class="col-md-3 col-6 form-paper">
            <div class="form-group">
                <label for="dateTo" class="form-paper-label">To</label>
                <input name="dateto" type="date" class="form-control form-control-sm" id="dateTo" value="{{ isset($details['dateto']) ? $details['dateto'] : old('dateto') }}" min="{{ isset($details['datefrom']) ? $details['datefrom'] : (old('datefrom') != null ? old('datefrom') : date_format(now(), 'Y-m-d')) }}" />
            </div>
        </div>
        <div class="col-md-3 form-paper">
            <div class="form-group mt-3">
                <input type="submit" value="Search" class="btn btn-sm btn-block btn-primary"/>
            </div>
        </div>
        <div class="col-12 form-paper section-divider"></div>
    </div>
</form>
<div class="row">
    @if (isset($details['name']))
    <div class="col-12 form-paper section-title">
        {{ $details['name'] }}
    </div>
    @endif
    <div class="col form-paper">
        <div style="overflow-x: auto" class="mb-4">
            @if (isset($records) && $records != null)
            <table class="table table-sm" id="dailyWorkingHoursTable" style="font-size:0.8em;">
                <thead>
                    <tr class="text-center">
                        <th rowspan="2">Timecard</th>
                        <th rowspan="2">Project/Department</th>
                        <th rowspan="2">Date</th>
                        <th colspan="2">Regular Time</th>
                        <th rowspan="2">Undertime</th>
                        <th rowspan="2">Total <br />Regular <br />Hours</th>
                        <th colspan="5">Overtime</th>
                        <th rowspan="2">ND</th>
                        <th rowspan="2">Remarks/Comments</th>
                    </tr>
                    <tr>
                        <th>In</th>
                        <th>Out</th>
                        <th>ROT</th>
                        <th>XOT</th>
                        <th>SOT</th>
                        <th>XSOT</th>
                        <th>LHOT</th>
                        <th>XLOT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $key => $record)
                    <?php
                        if ($record === null)
                            continue;
                    ?>
                    <tr>
                        <td>{{ $record->timecard }}</td>
                        <td>{{ $record->departmentName }}</td>
                        <td>{{ $key }}</td>
                        <td>{{ $record->timeIn != null ? date_format(date_create($record->timeIn), 'h:i A') : '' }}</td>
                        <td>{{ $record->timeOut != null ? date_format(date_create($record->timeOut), 'h:i A') : '' }}</td>
                        <td>{{ $record->undertime != null ? date_format(date_create($record->undertime), 'h:i A') : '' }}</td>
                        <td>{{ $record->regularHours }}</td>
                        <td>{{ $record->rot }}</td>
                        <td>{{ $record->xot }}</td>
                        <td>{{ $record->sot }}</td>
                        <td>{{ $record->xsot }}</td>
                        <td>{{ $record->lhot }}</td>
                        <td>{{ $record->xlhot }}</td>
                        <td>{{ $record->nd }}</td>
                        <td>{{ $record->remarks }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>
<div class="m-4">&nbsp;</div>

@stop

@section('script')
<script src="{{ asset('js/attendancePage.js') }}"></script>
@stop
