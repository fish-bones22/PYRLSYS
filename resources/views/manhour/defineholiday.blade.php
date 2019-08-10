@extends('layout.master')

@section('style')
<link href="{{ asset('vendors/jsCalendar/css/jsCalendar.min.css') }}" media="all" rel="stylesheet" type="text/css" />
<link href="{{ asset('vendors/timepicki/css/timepicki.css') }}" media="all" rel="stylesheet" type="text/css" />
@stop

@section('title')
Define Holidays
@stop


@section('content')

<div class="alert alert-danger" style="display:none"><span>Failed</span><button type="button" class="close" onclick="$('.alert').hide()">&times;</button></div>
<div class="alert alert-success" style="display:none">Successful<button type="button" class="close"  onclick="$('.alert').hide()">&times;</button></div>


<div class="row">
    <div class="col-lg-10 offset-lg-1 col-12">

        <div class="row">
            <div class="col form-paper section-title">Define Holiday</div>
        </div>
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
                    <div class="col-12">
                        <label for="name" class="form-paper-label">Name</label>
                        <input type="text" id="name" name="name" class="form-control" onchange="" />
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-paper-label">Description</label>
                        <textarea id="description" class="form-control" value=""></textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <div class="form-check-inline">
                                <label class="form-label"><input type="radio" name="type" class="form-radio" value="legal" /> Legal</label>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-label"><input type="radio" name="type" class="form-radio" value="special" /> Special</label>
                            </div>
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

        <div class="row">
            <div class="col-12 form-paper section-divider"></div>
            <div class="col-12 form-paper section-title">Holidays for {{ $year }}</div>
        </div>
        <div class="row">
            <div class="col-12 form-paper">
                <form action="{{ route('manhour.defineholiday') }}" method="post">
                    @csrf
                    @method('post')
                    <div class="form-group float-left">
                        <label for="year" class="form-paper-label">Year</label>
                        <div class="input-group">
                            <input type="number" min="1991" max="2100" id="year" class="form-control form-control-sm" name="year" value="{{ $year }}" />
                            <button type="submit" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-right"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @if ($holidays !== null && sizeof($holidays) > 0)
        <div class="row">
            <div class="col-12 form-paper section-divider"></div>
            <div class="col-12 form-paper">
                <div style="overflow-x: auto">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody id="holidayList">
                            @foreach ($holidays as $holiday)
                            <tr data-conditional="{{ $holiday['date'] }}" onclick="onTableRowClicked(this)">
                                <td>{{ date_format(date_create($holiday['date']), 'M d, Y') }}</td>
                                <td>{{ $holiday['name'] }}</td>
                                <td>{{ $holiday['type'] }}</td>
                                <td>{{ $holiday['description'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <div class="m-4">&nbsp;</div>
        <div class="fixed-bottom btn-container m-4">
            <div class="float-right">
                <div class="btn-group">
                    <a class="btn btn-light" href="{{ action('ManhourController@index') }}">Back to List</a>
                    <input type="button" id="deleteBtn" class="btn btn-secondary" value="Delete" style="display:none" />
                    <input type="button" id="saveBtn" class="btn btn-primary" value="Save"/>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('script')
<script src="{{ asset('vendors/jsCalendar/js/jsCalendar.min.js') }}"></script>
<script src="{{ asset('js/defineHoliday.js') }}"></script>
@stop
