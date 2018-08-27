@extends('layout.master')

@section('title')
{{date_format(date_create($details['date']), 'M Y') }} - Adjustments
@stop

@section('content')

<?php
$rcd = array();
foreach ($records as $record) {
    if (!isset($rcd[$record->details])) {
        $rcd[$record->details] = array();
    }

    $rcd[$record->details]['empId'] = $record->employee['id'];
    $rcd[$record->details]['name'] = $record->employee['name'];
    $rcd[$record->details]['amount'] = $record->amount;


}
?>

<div class="row">
    <div class="col-md-8 offset-md-2">

        <div class="row">
            <div class="col-12 form-paper section-title" id="title">{{ date_format(date_create($details['date']), 'M Y') }} - Adjustments</div>
        </div>
        <div class="row">
            <div class="col-12 form-paper">

                <form id="setDateForm" action="{{ action('AdjustmentsRecordController@getAllOnDate') }}" method="get">
                    @csrf
                    @method('get')
                    <div class="row">
                        <div class="col-5">
                            <div class="form-group">
                                <label class="form-paper-label">Period</label><br />
                                <div class="form-check-inline">
                                    <input id="secondPeriod" type="radio" name="period" value="second" {{ isset($details['startday']) && $details['startday'] <= 15 ? 'checked' : '' }} />
                                    <label for="secondPeriod" class="form-check-label small">Second (1-15)</label>
                                </div>
                                <div class="form-check-inline">
                                    <input id="firstPeriod" type="radio" name="period" value="first" {{ isset($details['startday']) && $details['startday'] >= 16 ? 'checked' : '' }} />
                                    <label for="firstPeriod" class="form-check-label small">First (16-EoM)</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="form-group">
                                <label class="form-paper-label">Month and Year</label>
                                <div class="input-group">
                                    @include('layout.monthselect', ['form' => 'setDateForm', 'monthSelected' => ( isset($details['month']) ? $details['month'] : date_format(now(), 'm') ) ])
                                    <input type="number" min="1991" max="2100" id="yearSelect" class="form-control form-control-sm" name="year" value="{{ isset($details['year']) ? $details['year'] : date_format(now(), 'Y') }}" />
                                    <button type="submit" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-right"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="col-12 form-paper section-divider"></div>
        </div>

        <div class="row">
            <div class="col-12 form-paper">
                <table class="table table-sm" id="AdjustmentsTable" style="font-size:11px;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            @if (sizeof($rcd) > 0)
                            @foreach ($rcd as $key => $record)
                            <th>{{$key}}</th>
                            @endforeach
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @foreach ($rcd as $key => $record)
                            @foreach ($record as $key => $rc)
                            <td>{{ $rc }}</td>
                            @endforeach
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
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
<script src="{{ asset('js/getAllAdjustmentsRecord.js') }}"></script>
@stop
