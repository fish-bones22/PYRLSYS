@extends('layout.master')

@section('style')
<link href="{{ asset('vendors/jsCalendar/css/jsCalendar.min.css') }}" media="all" rel="stylesheet" type="text/css" />
<link href="{{ asset('vendors/timepicki/css/timepicki.css') }}" media="all" rel="stylesheet" type="text/css" />
@stop

@section('title')
Manhour Input
@stop


@section('content')

@if (session('error') != null)
<div class="alert alert-danger">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif
@if (isset($success))
<div class="alert alert-success">{{ $success }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif

@if (!isset($employee->timeTable['timein']) || $employee->timeTable['timein'] == null || $employee->timeTable['timein'] == '')
<div class="alert alert-warning">Employee has no defined schedule, please set it <a href="{{ action('EmployeeController@view', ['id' => $employee->id ]) }}">here</a> first to avoid unexpected behaviors</div>
<input type="hidden" id="hasinvalidwarning" value="true" />
@endif

<div class="row">
    <div class="col-lg-10 offset-lg-1 col-12">

        <form action="{{ action('ManhourController@search', $employee->id) }}" method="GET">
            <div class="row">
                <div class="form-paper col-12">
                    <div class="row">
                        <div class="col-sm-6 col-md-4 offset-sm-6 offset-md-8">
                            <div class="form-group float-right">
                                <label class="form-paper-label">Search</label>
                                <div class="input-group">
                                    <input type="search" name="search" class="form-control form-control-sm" />
                                    <button type="submit" class="btn btn-sm btn-secondary">Go</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <form action="{{ action('ManhourController@record', $employee->id) }}" method="POST">
            @csrf
            @method('post')

            <div class="row">
                <div class="col form-paper section-title">Employee</div>
            </div>
            <div class="row">
                <div class="col-2 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Timecard</label>
                        <div class="form-paper-display" id="timeCardDisplay">{!! key_exists('timecard', $employee->current) ? $employee->current['timecard'] : '<i class="text-muted small">No Timecard</i>' !!}</div>
                        <input type="hidden" name="time_card" value="{{ key_exists('timecard', $employee->current) ? $employee->current['timecard'] : '' }}" />
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Employee</label>
                        <div class="form-paper-display">{{ $employee->fullName != null ? $employee->fullName : 'Employee Name' }}</div>
                        <input type="hidden" name="full_name" value="{{ $employee->fullName != null ? $employee->fullName : '' }}" />
                        <input type="hidden" id="employeeId" name="id" value="{{ $employee->id != null ? $employee->id : 0 }}" />
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Department</label>
                        <div class="form-paper-display"  id="departmentNameDisplay">{!! key_exists('department', $employee->current) ? $employee->current['department']['displayName'] : '<i class="text-muted small">No Department</i>' !!}</div>
                        <input type="hidden" id="departmentIdDisplay" name="department" value="{{ key_exists('department', $employee->current) ? $employee->current['department']['value'] : '' }}" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col form-paper section-title">Date and Time</div>
            </div>
            <?php

            // Raw strings
            $scheduledTimeInStr = key_exists('timein', $employee->timeTable) ? $employee->timeTable['timein'] : '';
            $scheduledTimeOutStr = key_exists('timeout', $employee->timeTable) ? $employee->timeTable['timeout'] : '';
            // Date object
            $scheduledTimeIn_ = $scheduledTimeInStr != '' ? date_create($scheduledTimeInStr) : null;
            $scheduledTimeOut_ = $scheduledTimeOutStr != '' ? date_create($scheduledTimeOutStr) : null;
            // hh:mm tt
            $scheduledTimeIn = $scheduledTimeIn_ != null ? date_format($scheduledTimeIn_, 'h:i A') : '';
            $scheduledTimeOut = $scheduledTimeOut_ != null ? date_format($scheduledTimeOut_, 'h:i A') : '';
            // HH:mm
            $scheduledTimeInUnformatted = $scheduledTimeIn_ != null ? date_format($scheduledTimeIn_, 'H:i') : '';
            $scheduledTimeOutUnformatted = $scheduledTimeOut_ != null ? date_format($scheduledTimeOut_, 'H:i') : '';

            ?>
            <div class="row">
                <div class="col-5  form-paper">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-paper-label">Date</label>
                                <input type="text" id="dateDisplay" class="form-control" readonly required />
                                <input type="hidden" id="date" name="date" class="form-control" required />
                                <i class="text-danger small" id="warningText">Select date on calendar</i>
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <label for="timeIn" class="form-paper-label">Time In</label>
                            <input type="time" id="timeIn" name="time_in" class="{{--timepicker--}} form-control" onchange="setHour()" value="{{ old('time_in') }}" />
                        </div>
                        <div class="col-sm-5">
                            <label for="timeIn" class="form-paper-label">Scheduled In</label>
                            <input type="hidden" id="scheduledTimeInHidden" value="{{ $scheduledTimeInUnformatted }}" />
                            <div id="scheduledTimeIn" class="form-paper-display">{{ $scheduledTimeInUnformatted }}</div>
                        </div>
                        <div class="col-sm-7">
                            <label for="timeOut" class="form-paper-label">Time Out</label>
                            <input type="time" id="timeOut" name="time_out" class="{{--timepicker--}} form-control" onchange="setHour()" value="{{ old('time_out') }}"d />
                        </div>
                        <div class="col-sm-5">
                            <label for="timeOut" class="form-paper-label">Scheduled Out</label>
                            <input type="hidden" id="scheduledTimeOutHidden" value="{{ $scheduledTimeOutUnformatted }}" />
                            <div id="scheduledTimeOut" class="form-paper-display">{{ $scheduledTimeOutUnformatted }}</div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="hour" class="form-paper-label">Hours</label>
                                <input type="number" id="hour" name="hours" class="form-control" tabindex="-1" onkeyup="setTimeOutByHour()" step="0.01" />
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="hour" class="form-paper-label">Counted Hours</label>
                                <input type="number" id="counted-hour" class="form-control" readonly />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-7 form-paper">
                    <div class="form-group">
                        <div id="calendar" class="classic-theme green" align="center" ></div>
                    </div>
                </div>
            </div>
            <div class="row"><div class="col form-paper section-title">Outliers</div></div>
            <div class="row">
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-12 border-bottom mb-1">
                                <label class="form-label">
                                    <input type="checkbox" name="authorized" /> Authorized
                                </label>
                            </div>
                        </div>
                        <div style="max-height:170px;">
                            <div class="" style="overflow-y: scroll; max-height: 120px;">

                                @foreach ($outliers as $outlier)

                                <?php
                                    $checked = '';
                                    if (old('outlier') == $outlier->id) {
                                        $checked = 'checked';
                                    }
                                ?>

                                <div class="">
                                    <label class="form-label">
                                        <input type="radio" name="outlier" class="form-radio" value="{{ $outlier->id }}" {{ $checked }}/> {{ $outlier->value }}</label>
                                </div>

                                @endforeach

                            </div>
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-light btn-sm btn-block" onclick="resetOutliers()">Reset</button>
                        </div>
                    </div>
                </div>
                <div class="col-8 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Remarks</label>
                        <textarea id="remarks" name="remarks" class="form-control">{{ old('remarks') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="m-4">&nbsp;</div>
            <div class="fixed-bottom btn-container m-4">
                <div class="float-right">
                    <div class="btn-group">
                        <a class="btn btn-light" href="{{ action('ManhourController@index') }}">Back to List</a>
                        <a class="btn btn-secondary" href="{{ action('ManhourController@inputAll', date_format(now(), 'Y-m-d')) }}">Batch Input</a>
                        <a class="btn btn-secondary" href="{{ action('ManhourController@getPrev', $employee->id) }}">Back</a>
                        <a class="btn btn-secondary" href="{{ action('ManhourController@getNext', $employee->id) }}">Next</a>
                        <input type="submit" class="btn btn-primary" value="Save"/>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>
@stop

@section('script')
<script src="{{ asset('vendors/jsCalendar/js/jsCalendar.min.js') }}"></script>
<script src="{{ asset('vendors/timepicki/js/timepicki.js') }}"></script>
<script src="{{ asset('js/recordInputPage.js') }}"></script>
@stop
