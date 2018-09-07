@extends('layout.master')

<?php
$title = 'Daily Working Hours - '.(isset($date['mode']) && !$date['mode'] ? date_format(date_create($date['datefrom']), 'F d, Y') : date_format(date_create($date['datefrom']), 'F Y'));
?>

@section('title')
{{ $title }}
@stop

@section('content')

<div class="row">
    <div class="col-12 form-paper section-title" id="title">{{ $title }}</div>
    <div class="col-12 form-paper section-divider"></div>
</div>
<div class="row">
    {{-- <div class="col-2 form-paper">
        <div class="form-group">
            <label class="form-check-label">
                <input form="filterForm" type="radio" class="form-radio" value="daily" name="mode" onchange="toggleMode()" {{ isset($date['mode']) && !$date['mode'] ? 'checked' : '' }}> Daily Record
            </label>
            <label class="form-check-label">
                <input form="filterForm" type="radio" class="form-radio" value="monthly" name="mode" onchange="toggleMode()" {{ isset($date['mode']) && $date['mode'] ? 'checked' : '' }}> Monthly Record
            </label>
        </div>
    </div> --}}
    <div class="col form-paper" id="dailyRow"  {{ isset($date['mode']) && $date['mode'] ? 'style=display:none' : '' }}>
        <div class="form-group ">
            <label for="searchBox" class="form-paper-label">Date</label>
            <form id="filterForm" action="{{ route('manhour.filterdate') }}" method="POST">
                @csrf
                @method('post')
                <div class="input-group">
                    <input type="date" class="form-control form-control-sm" id="dateSelect" name="date" value="{{ isset($date['datefrom']) ? $date['datefrom'] : date_format(now(), 'Y-m-d') }}" />
                    <button type="submit" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-right"></i></button>
                </div>
            </form>
        </div>
    </div>
    {{-- <div class="col form-paper" {{ isset($date['mode']) && !$date['mode'] ? 'style=display:none' : '' }} id="monthlyRow">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="monthSelect" class="form-paper-label">Month and Year</label>
                    <div class="input-group">
                        @include('layout.monthselect', ['form' => 'filterForm', 'monthSelected' => isset($date['month']) ? $date['month'] : date_format(now(), 'm') ])
                        <input form="filterForm" type="number" min="1991" max="2100" id="yearSelect" class="form-control form-control-sm" name="year" value="{{ isset($date['year']) ? $date['year'] : date_format(now(), 'Y') }}" />
                        <button form="filterForm" type="submit" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="col-4 form-paper">
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label for="dapartment" class="form-paper-label">Dapartment</label>
                    <select class="form-control form-control-sm" id="dapartment" onchange="filterDepartment()">
                        <option value="0">All</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group float-right">
                    <label for="searchBox" class="form-paper-label">Search</label>
                    <input type="search" class="form-control form-control-sm" id="searchBox" onkeyup="filterRecords()" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 form-paper section-divider"></div>
</div>
<div class="row">
    <div class="col form-paper">
        <input type="hidden" name="date" value="{{$date['datefrom']}}" />
        <table class="table table-sm" id="dailyWorkingHoursTable" style="font-size:0.8em;">
            <thead>
                <tr class="text-center">
                    <th rowspan="2">Timecard</th>
                    <th rowspan="2">Employee Name</th>
                    <th rowspan="2">Project/Department</th>
                    {!! isset($date['mode']) && $date['mode'] == true ? '<th rowspan="2">Date</th>' : '' !!}
                    <th colspan="2">Regular Time</th>
                    <th rowspan="2">Undertime</th>
                    <th rowspan="2">Outlier</th>
                    <th rowspan="2">Authorized</th>
                    <th rowspan="2">Total <br />Regular <br />Hours</th>
                    <th colspan="5">Overtime</th>
                    <th rowspan="2">ND</th>
                    <th rowspan="2">Remarks/Comments</th>
                </tr>
                <tr>
                    <th>In</th>
                    <th>Out</th>
                    <th>ROT</th>
                    <th>SOT</th>
                    <th>XSOT</th>
                    <th>LHOT</th>
                    <th>XLOT</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $index = 0;
                ?>
                @foreach ($records as $record)
                <?php
                    if ($record === null)
                        continue;
                ?>
                <tr>
                    <td>
                        {{ $record->timecard }}
                        <input type="hidden" name="time_card[{{$index}}]" value="{{ $record->timecard }}" />
                    </td>
                    <td>
                        {{ $record->employeeName }}
                        <input type="hidden" name="employee_id[{{$index}}]" value="{{ $record->employee_id }}" />
                        <input type="hidden" name="employee_name[{{$index}}]" value="{{ $record->employeeName }}" />
                    </td>
                    <td>
                        {{ $record->departmentName }}
                        <input type="hidden" name="department[{{$index}}]" value="{{ $record->departmentId }}" />
                    </td>
                    {!! isset($date['mode']) && $date['mode'] == true ? '<td>'.$record->date.'</td>' : '' !!}
                    <td><input type="time" class="form-control form-control-sm" name="time_in[{{$index}}]" value="{{ $record->timeIn }}" /></td>
                    <td><input type="time" class="form-control form-control-sm" name="time_out[{{$index}}]" value="{{ $record->timeOut }}" /></td>
                    <td><input type="time" class="form-control form-control-sm" name="time_out_undertime[{{$index}}]" value="{{ $record->undertime }}" /></td>
                    <td>
                        <select class="form-control form-control-sm" name="outlier[{{$index}}]">
                            <option></option>
                            @foreach ($outliers as $outlier)
                            <option value="{{ $outlier->id }}" {{ $outlier->id == $record->outlierId ? 'selected' : '' }}>{{ $outlier->value }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <label class="form-label">
                            <input type="checkbox" name="authorized[{{$i}}]" /> Authorized
                        </label>
                    </td>
                    <td>{{ $record->regularHours }}</td>
                    <td>{{ $record->rot }}</td>
                    <td>{{ $record->sot }}</td>
                    <td>{{ $record->xsot }}</td>
                    <td>{{ $record->lhot }}</td>
                    <td>{{ $record->xlhot }}</td>
                    <td>{{ $record->nd }}</td>
                    <td><input type="text" name="remarks[{{$i}}]" class="form-control form-control-sm" value="{{ $record->remarks }}" /></td>
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
            {{-- <a class="btn btn-light" href="{{ action('EmployeeController@index') }}">Back to List</a> --}}
            <button type="button" class="btn btn-primary" onclick="saveAsPDF()">Save as PDF</button>
            <button type="button" class="btn btn-primary" onclick="saveAsExcel()">Save as Excel</button>
        </div>
    </div>
</div>

@stop

@section('script')
<script src="{{ asset('js/dailyRecordPage.js') }}"></script>
@stop
