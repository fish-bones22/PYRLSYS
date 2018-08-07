@extends('layout.master')

@section('style')
<link href="{{ asset('vendors/jsCalendar/css/jsCalendar.min.css') }}" media="all" rel="stylesheet" type="text/css" />
<link href="{{ asset('vendors/timepicki/css/timepicki.css') }}" media="all" rel="stylesheet" type="text/css" />
@stop

@section('title')
Manhour Input
@stop

@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1">


        <div class="row">
            <div class="col form-paper section-title">Employee</div>
        </div>
        <div class="row">
            <div class="col-8 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">Employee</label>
                    <div class="form-paper-display">Employee Name</div>
                </div>
            </div>
            <div class="col-4 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">Department</label>
                    <div class="form-paper-display">Employee Dept.</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col form-paper section-title">Date and Time</div>
        </div>
        <div class="row">
            <div class="col-4  form-paper">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-paper-label">Date</label>
                            <input type="text" id="dateDisplay" class="form-control" readonly required />
                            <input type="hidden" id="date" name="date" class="form-control" />
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="timeIn" class="form-paper-label">Time In</label>
                        <input type="time" id="timeIn" name="time_in" class="timepicker form-control" onchange="setHour()" required />
                        {{-- <input type="text" name="time_in" class="time_element" /> --}}
                    </div>
                    <div class="col-12">
                        <label for="timeOut" class="form-paper-label">Time Out</label>
                        <input type="time" id="timeOut" name="time_out" class="timepicker form-control" onchange="setHour()" required />
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="hour" class="form-paper-label">Hours</label>
                            <input type="number" id="hour" name="hours" class="form-control" readonly/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-8 form-paper">
                <div class="form-group">
                    <div id="calendar" class="classic-theme green" align="center" ></div>
                </div>
            </div>
        </div>
        <div class="row"><div class="col form-paper section-title">Outliers</div></div>
        <div class="row">
            <div class="col form-paper">
                <div class="row">
                    <div class="col">
                        <div class="form-group form-check-inline">
                            <label class="form-label">
                                <input type="radio" name="outlier" class="form-radio" value="Absent"/> Absent
                            </label>
                        </div>
                    </div>
                    <div class="col">
                            <div class="form-group form-check-inline">
                            <label class="form-label">
                                <input type="radio" name="outlier" class="form-radio" value="Late"/> Late
                            </label>
                        </div>
                    </div>
                    <div class="col">
                            <div class="form-group form-check-inline">
                            <label class="form-label">
                                <input type="radio" name="outlier" class="form-radio" value="On Leave"/> On Leave
                            </label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <button type="button" class="btn btn-light btn-sm" onclick="resetOutliers()">Reset</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@stop

@section('script')
<script src="{{ asset('vendors/jsCalendar/js/jsCalendar.min.js') }}"></script>
<script src="{{ asset('vendors/timepicki/js/timepicki.js') }}"></script>
<script src="{{ asset('js/dateSelector.js') }}"></script>
@stop
