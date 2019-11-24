@extends('layout.master')

<?php
$title = 'Manhour Input - '.(date_format(date_create($details['date']), 'F Y'));
?>

@section('title')
{{ $title }}
@stop

@section('content')

<div class="row">
    <div class="col-12 form-paper section-title" id="title">{{ $title }}
        <span class="float-right">
            <a href="{{ action('ManhourController@inputCsv') }}" class="btn btn-link btn-clipping">Use CSV file</a>
        </span>
    </div>
    <div class="col-12 form-paper section-divider"></div>
</div>
<div class="row">
    <div class="col-6 form-paper">
        <div class="form-group ">
            <label for="searchBox" class="form-paper-label">Date</label>
            <form id="filterForm" action="{{ route('manhour.filterdateall') }}" method="POST">
                @csrf
                @method('post')
                <div class="input-group">
                    <input type="date" class="form-control form-control-sm" id="dateSelect" name="date" value="{{ isset($details['date']) ? $details['date'] : date_format(now(), 'Y-m-d') }}" />
                    <button type="submit" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-right"></i></button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-6 form-paper">
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

<form action="{{ action('ManhourController@recordAll') }}" method="POST">
    @csrf
    @method('post')
    <div class="row">
        <div class="col form-paper">
            <div style="overflow-x:auto" class=" mb-4">
                <input type="hidden" name="date" value="{{$details['date']}}" />
                <table class="table table-sm" id="dailyWorkingHoursTable" style="font-size:0.75em;">
                    <thead>
                        <tr class="text-center">
                            <th rowspan="2">Timecard</th>
                            <th rowspan="2">Employee Name</th>
                            <th rowspan="2">Project/Department</th>
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
                    <tbody class="">
                        <?php
                            $index = 0;
                        ?>
                        @foreach ($records as $record)
                        <?php
                            //if ($record === null)
                        ?>
                        <tr {{ $record != null && $record->regularHours != null ? "class=\"highlighted\"" : ''}} >
                            <td>
                                {{ $record != null ? $record->timecard : '' }}
                                <input type="hidden" name="time_card[{{$index}}]" value="{{  $record != null ? $record->timecard : ''}}" />
                            </td>
                            <td>
                                {{  $record != null ? $record->employeeName : ''}}
                                <input type="hidden" name="employee_id[{{$index}}]" value="{{  $record != null ? $record->employee_id : ''}}" />
                                <input type="hidden" name="employee_name[{{$index}}]" value="{{  $record != null ? $record->employeeName : ''}}" />
                            </td>
                            <td>
                                {{  $record != null ? $record->departmentName : ''}}
                                <input type="hidden" name="department[{{$index}}]" value="{{ $record != null ?  $record->departmentId : '' }}" />
                            </td>
                            <td><input type="time" class="form-control form-control-sm form-control-sh" name="time_in[{{$index}}]" value="{{  $record != null ? $record->timeIn : ''}}" tabindex="1" style="min-width:100px" /></td>
                            <td><input type="time" class="form-control form-control-sm form-control-sh" name="time_out[{{$index}}]" value="{{  $record != null ? $record->timeOut : ''}}" tabindex="1" style="min-width:100px"/></td>
                            <td><input type="time" class="form-control form-control-sm form-control-sh" name="time_out_undertime[{{$index}}]" value="{{  $record != null ? $record->undertime : '' }}" tabindex="-1" style="min-width:100px"/></td>
                            <td>
                                <select class="form-control form-control-sm" name="outlier[{{$index}}]" tabindex="-1" style="min-width:100px">
                                    <option></option>
                                    @foreach ($outliers as $outlier)
                                    <option value="{{ $outlier->id }}" {{  $record != null && $outlier->id == $record->outlierId ? 'selected' : '' }}>{{ $outlier->value }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <label class="form-label">
                                    <input type="checkbox" name="authorized[{{$index}}]" {{ $record->authorized != null ? 'checked' : '' }} tabindex="-1" /> Yes
                                </label>
                            </td>
                            <td>{{ $record != null ? $record->regularHours : '' }}</td>
                            <td>{{ $record != null ? $record->rot : ''}}</td>
                            <td>{{ $record != null ? $record->sot : ''}}</td>
                            <td>{{ $record != null ? $record->xsot : ''}}</td>
                            <td>{{ $record != null ? $record->lhot : ''}}</td>
                            <td>{{ $record != null ?  $record->xlhot : '' }}</td>
                            <td>{{ $record != null ? $record->nd : '' }}</td>
                            <td><input type="text" name="remarks[{{$index}}]" class="form-control form-control-sm" value="{{ $record != null ? $record->remarks : '' }}" tabindex="-1" /></td>
                        </tr>
                        <?php
                        $index++;
                        ?>
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
                <a class="btn btn-light" href="{{ action('ManhourController@index') }}">Back to List</a>
                <a class="btn btn-secondary" href="{{ route('manhour.input') }}">Individual Input</a>
                <button type="submit" class="btn btn-primary" data-confirm="save">Save</button>
            </div>
        </div>
    </div>
</form>

@stop

@section('script')
<script src="{{ asset('js/inputAllPage.js') }}"></script>
@stop
