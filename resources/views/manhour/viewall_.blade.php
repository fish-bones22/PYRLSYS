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
    <div class="col-2 form-paper">
        <div class="form-group">
            <label class="form-check-label">
                <input form="filterForm" type="radio" class="form-radio" value="daily" name="mode" onchange="toggleMode()" {{ isset($date['mode']) && $date['mode'] == 'daily' ? 'checked' : '' }}> Daily Record
            </label>
            <label class="form-check-label">
                <input form="filterForm" type="radio" class="form-radio" value="monthly" name="mode" onchange="toggleMode()" {{ isset($date['mode']) && $date['mode'] == 'monthly' ? 'checked' : '' }}> Monthly Record
            </label>
            <label class="form-check-label">
                <input form="filterForm" type="radio" class="form-radio" value="periodic" name="mode" onchange="toggleMode()" {{ isset($date['mode']) && $date['mode'] == 'periodic' ? 'checked' : '' }}> Periodical Record
            </label>
            <label class="form-check-label">
                <input form="filterForm" type="radio" class="form-radio" value="daterange" name="mode" onchange="toggleMode()" {{ isset($date['mode']) && $date['mode'] == 'daterange' ? 'checked' : '' }}> Date Range
            </label>
        </div>
    </div>
    <div class="col form-paper" id="dailyRow"  {{ isset($date['mode']) && $date['mode'] === 'daily' ? '' : 'style=display:none' }}>
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
    <div class="col form-paper" {{ isset($date['mode']) && $date['mode'] === 'monthly' ? '' : 'style=display:none' }} id="monthlyRow">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="monthSelect" class="form-paper-label">Month and Year</label>
                    <div class="input-group">
                        @include('layout.monthselect', ['form' => 'filterForm', 'monthSelected' => isset($date['month']) ? $date['month'] : date_format(now(), 'm'), 'name' => 'month' ])
                        <input form="filterForm" type="number" min="1991" max="2100" id="yearSelect" class="form-control form-control-sm" name="year" value="{{ isset($date['year']) ? $date['year'] : date_format(now(), 'Y') }}" />
                        <button form="filterForm" type="submit" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col form-paper" {{ isset($date['mode']) && $date['mode'] === 'periodic' ? '' : 'style=display:none' }} id="periodicRow">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-paper-label">Period</label><br />
                    <div class="form-check-inline">
                        <input form="filterForm" id="secondPeriod" type="radio" name="period" value="second" {{ isset($date['startday']) && $date['startday'] <= 15 ? 'checked' : '' }} />
                        <label for="secondPeriod" class="form-check-label small">Second (1-15)</label>
                    </div>
                    <div class="form-check-inline">
                        <input form="filterForm" id="firstPeriod" type="radio" name="period" value="first" {{ isset($date['startday']) && $date['startday'] >= 16 ? 'checked' : '' }} />
                        <label for="firstPeriod" class="form-check-label small">First (16-EoM)</label>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="monthPeriodSelect" class="form-paper-label">Month and Year</label>
                    <div class="input-group">
                        @include('layout.monthselect', ['form' => 'filterForm', 'monthSelected' => isset($date['month']) ? $date['month'] : date_format(now(), 'm'), 'name' => 'month_period' ])
                        <input form="filterForm" type="number" min="1991" max="2100" id="yearSelect" class="form-control form-control-sm" name="year_period" value="{{ isset($date['year']) ? $date['year'] : date_format(now(), 'Y') }}" />
                        <button form="filterForm" type="submit" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col form-paper" {{ isset($date['mode']) && $date['mode'] === 'daterange' ? '' : 'style=display:none' }} id="daterangeRow">
        <div class="form-group">
            <label for="monthSelect" class="form-paper-label">Date Range</label>
            <div class="input-group">
                <input form="filterForm" type="date" id="dateFrom" class="form-control form-control-sm" name="date_from" value="{{ isset($date['datefrom']) ? $date['datefrom'] : date_format(now(), 'Y-m-d') }}" />
                <input form="filterForm" type="date" id="dateTo" class="form-control form-control-sm" name="date_to" value="{{ isset($date['dateto']) ? $date['dateto'] : '' }}" />
                <button form="filterForm" type="submit" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-right"></i></button>
            </div>
        </div>
    </div>
    <div class="col-4 form-paper">
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label for="department" class="form-paper-label">Department</label>
                    <select class="form-control form-control-sm" id="department" onchange="filterDepartment()">
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
        <div style="overflow-x: auto">
            <table class="table table-sm" id="dailyWorkingHoursTable" style="font-size:0.8em;">
                <thead>
                    <tr class="text-center">
                        <th rowspan="2">Timecard</th>
                        <th rowspan="2">Employee Name</th>
                        <th rowspan="2">Project/Department</th>
                        {!! isset($date['mode']) && $date['mode'] == true ? '<th rowspan="2">Date</th>' : '' !!}
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
                        <th>SOT</th>
                        <th>XSOT</th>
                        <th>LHOT</th>
                        <th>XLOT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                    <?php
                        if ($record === null)
                            continue;
                    ?>
                    <tr>
                        <td>{{ $record->timecard }}</td>
                        <td>{{ $record->employeeName }}</td>
                        <td>{{ $record->departmentName }}</td>
                        {!! isset($date['mode']) && $date['mode'] == true ? '<td>'.$record->date.'</td>' : '' !!}
                        <td>{{ $record->timeIn != null ? date_format(date_create($record->timeIn), 'h:i A') : '' }}</td>
                        <td>{{ $record->timeOut != null ? date_format(date_create($record->timeOut), 'h:i A') : '' }}</td>
                        <td>{{ $record->undertime != null ? date_format(date_create($record->undertime), 'h:i A') : '' }}</td>
                        <td>{{ $record->regularHours }}</td>
                        <td>{{ $record->rot }}</td>
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
        </div>
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
