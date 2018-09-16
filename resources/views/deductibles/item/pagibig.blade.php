@extends('layout.master')

@section('title')
{{date_format(date_create($details['date']), 'M Y') }} - SSS Remittance
@stop

@section('content')

<?php
$rcd = array();
foreach ($records as $record) {
    if (!isset($rcd[$record->employee['id']])) {
        $rcd[$record->employee['id']] = [
            'employeeId' => $record->employee['employeeId'],
            'name' => $record->employee['name']
        ];
    }

    $rcd[$record->employee['id']][$record->key] = [
        'employee' => $record->amount,
        'employer' => $record->subamount
    ];


}
?>

<div class="row">
    <div class="col-md-8 offset-md-2">

        <div class="row">
            <div class="col-12 form-paper section-title" id="title">{{ date_format(date_create($details['date']), 'M Y') }} - SSS Remittance</div>
        </div>
        <div class="row">
            <div class="col-12 form-paper">

                <form id="setDateForm" action="{{ action('DeductibleRecordController@getAllOnDate') }}" method="get">
                    @csrf
                    @method('get')
                    <div class="row">
                        {{-- <div class="col-5">
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
                        </div> --}}
                        <div class="col-12">
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
                <table class="table table-sm" id="deductiblesTable" style="font-size:11px;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>SS Number</th>
                            <th>Date of Coverage</th>
                            <th>Basic Salary</th>
                            <th>Emp 16-EoM</th>
                            <th>Emp 1-15</th>
                            <th>Emp Total</th>
                            <th>Emr 16-EoM</th>
                            <th>Emr 1-15</th>
                            <th>Emr Total</th>
                            <th>Total Remmitance</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sssEmp = 0;
                        $philhealthEmp = 0;
                        $pagibigEmp = 0;
                        $sssEmr = 0;
                        $philhealthEmr = 0;
                        $pagibigEmr = 0;
                        ?>
                        @foreach ($rcd as $key => $record)
                        <?php
                        $sssEmp += (isset($record['sss']) ? $record['sss']['employee'] : 0);
                        $philhealthEmp += (isset($record['sss']) ? $record['philhealth']['employee'] : 0);
                        $pagibigEmp += (isset($record['sss']) ? $record['pagibig']['employee'] : 0);
                        $sssEmr += (isset($record['sss']) ? $record['sss']['employer'] : 0);
                        $philhealthEmr += (isset($record['sss']) ? $record['philhealth']['employer'] : 0);
                        $pagibigEmr += (isset($record['sss']) ? $record['pagibig']['employer'] : 0);
                        ?>
                            <tr>
                                <td>{{ $record['employeeId'] }}</td>
                                <td>{{ $record['name'] }}</td>
                                <td>{{ isset($record['sss']) ? $record['sss']['employee'] : '' }}</td>
                                <td>{{ isset($record['sss']) ? $record['sss']['employer'] : '' }}</td>
                                <td>{{ isset($record['philhealth']) ? $record['philhealth']['employee'] : '' }}</td>
                                <td>{{ isset($record['philhealth']) ? $record['philhealth']['employer'] : '' }}</td>
                                <td>{{ isset($record['pagibig']) ? $record['pagibig']['employee'] : '' }}</td>
                                <td>{{ isset($record['pagibig']) ? $record['pagibig']['employer'] : '' }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td>TOTAL</td>
                            <td></td>
                            <td>{{ $sssEmp }}</td>
                            <td>{{ $sssEmr }}</td>
                            <td>{{ $philhealthEmp }}</td>
                            <td>{{ $philhealthEmr }}</td>
                            <td>{{ $pagibigEmp }}</td>
                            <td>{{ $pagibigEmr }}</td>
                            <td></td>
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
<script src="{{ asset('js/getAllDeductibleRecord.js') }}"></script>
@stop
