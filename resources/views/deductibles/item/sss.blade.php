@extends('layout.master')

<?php
$_key = $details['key'];
?>

@section('title')
{{date_format(date_create($details['date']), 'M Y') }} - {{ strtoupper($_key) }} Remittance
@stop

@section('content')

<?php
$rcd = array();
foreach ($records as $record) {
    if (!isset($rcd[$record->employee['id']])) {
        $rcd[$record->employee['id']] = [
            'employeeId' => $record->employee['employeeId'],
            'lastName' => $record->employee['lastname'],
            'firstName' => $record->employee['firstname'],
            'middleName' => $record->employee['middlename'],
            'basicsalary' => $record->employee['basicsalary'],
            'basis' => $record->employee['basis'],
            'department' => $record->employee['department']
        ];
    }

    $rcd[$record->employee['id']][$record->key] = [
        'employee' => $record->amount,
        'employer' => $record->subamount,
        'subamount2' => $record->subamount2,
        'identifier' => isset($record->identifier['value']) ? $record->identifier['value'] : '',
        'identifierName' => isset($record->identifier['details']) ? $record->identifier['details'] : '',
        'remarks' => $record->remarks
    ];


}
$rcd2 = array();
foreach ($records2 as $record) {
    if (!isset($rcd[$record->employee['id']])) {
        $rcd2[$record->employee['id']] = [
            'employeeId' => $record->employee['employeeId'],
            'lastName' => $record->employee['lastname'],
            'firstName' => $record->employee['firstname'],
            'middleName' => $record->employee['middlename'],
            'basicsalary' => $record->employee['basicsalary'],
            'department' => $record->employee['department']
        ];
    }

    $rcd2[$record->employee['id']][$record->key] = [
        'employee' => $record->amount,
        'employer' => $record->subamount,
        'subamount2' => $record->subamount2,
        'identifier' => isset($record->identifier['value']) ? $record->identifier['value'] : ''
    ];


}
?>

<div class="row">
    <div class="col-md-12">

        <div class="row">
            <div class="col-12 form-paper section-title" id="title">{{ date_format(date_create($details['date']), 'M Y').' - '.date_format(date_create($details['date2']), 'M Y') }} - {{ strtoupper($_key) }} Remittance</div>
        </div>
        <div class="row">
            <div class="col-12 form-paper">

                <form id="setDateForm" action="{{ action('DeductibleRecordController@goToDateView') }}" method="get">
                    @csrf
                    @method('get')
                    <input type="hidden" name="key" value="{{ $_key }}" />
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-paper-label">Month and Year</label>
                                <div class="input-group">
                                    @include('layout.monthselect', ['form' => 'setDateForm', 'monthSelected' => ( isset($details['month']) ? $details['month'] : date_format(now(), 'm') ), 'name' => 'month' ])
                                    <input type="number" min="1991" max="2100" id="yearSelect" class="form-control form-control-sm" name="year" value="{{ isset($details['year']) ? $details['year'] : date_format(now(), 'Y') }}" />
                                    <button type="submit" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-right"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
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
                        <div class="col-3">
                            <div class="form-group float-right">
                                <label for="searchBox" class="form-paper-label">Search</label>
                                <input id="searchBox" type="search" class="form-control form-control-sm" onkeyup="filterEmployees()" />
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
                            <th>Department</th>
                            <th>{{ strtoupper($_key) }} Number</th>
                            <th>Date of Coverage</th>
                            <th>Basic Salary</th>
                            <th>Emp <br />16-EoM</th>
                            <th>Emp <br />1-15</th>
                            <th>Emp Total</th>
                            <th>Emr <br />Share</th>
                            @if ($_key === 'sss')
                            <th>Emr <br />EC</th>
                            @endif
                            <th>Total <br />Remmitance</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $emp1 = 0;
                        $emr1 = 0;
                        $emp2 = 0;
                        $emr2 = 0;
                        $subamount2 = 0;
                        $empTotal1 = 0;
                        $empTotal2 = 0;
                        $empTotal = 0;
                        $emrTotal = 0;
                        $emcTotal = 0;
                        $grandTotal = 0;
                        ?>
                        @foreach ($rcd as $key => $record)
                        <?php
                        $emp1 = (isset($record[$_key]) ? $record[$_key]['employee'] : 0);
                        $emr1 = (isset($record[$_key]) ? $record[$_key]['employer'] : 0);
                        $emp2 = (isset($rcd2[$key][$_key]) ? $rcd2[$key][$_key]['employee'] : 0);
                        $emr2 = (isset($rcd2[$key][$_key]) ? $rcd2[$key][$_key]['employer'] : 0);
                        $emp = $emp1 + $emp2;
                        $emr = $emr1 + $emr2;
                        $emc = (isset($record[$_key]) ? $record[$_key]['subamount2'] : '0') + (isset($rcd2[$key][$_key]['subamount2']) ? $rcd2[$key][$_key]['subamount2'] : '0');
                        $total = $emp + $emc + $emr;
                        $empTotal1 += $emp1;
                        $empTotal2 += $emp2;
                        $empTotal += $emp1 + $emp2;
                        $emrTotal += $emr;
                        $emcTotal += $emc;
                        $grandTotal += $total;
                        ?>
                            <tr>
                                <td>{{ $record['employeeId'] }}</td>
                                <td>{{ $record['lastName'] }}</td>
                                <td>{{ $record['firstName'] }}</td>
                                <td>{{ $record['middleName'] }}</td>
                                <td>{{ $record['department'] }}</td>
                                <td>{{ isset($record[$_key]) ? $record[$_key]['identifier'] : '' }}</td>
                                <td>{{ isset($details['date']) ? date_format(date_create($details['date']), 'M Y')  : '' }}</td>
                                {{-- Basic Salary --}}<td>{{ isset($record['basicsalary']) ? $record['basicsalary'].' '.$record['basis'] : '' }}</td>
                                {{-- Emp 16-EoM --}}<td>{{ $emp1 }}</td>
                                {{-- Emp 1-16 --}}<td>{{ $emp2 }}</td>
                                {{-- Emp Total --}}<td>{{ $emp }}</td>
                                {{-- Emr --}}<td>{{ $emr }}</td>
                                @if ($_key === 'sss')
                                {{-- Emr EC --}}<td>{{ $emc }}</td>
                                @endif
                                {{-- Total --}}<td>{{ $total }}</td>
                                <td>{{ isset($record['remarks']) ? $record['remarks'] : '' }}</td>
                            </tr>
                        @endforeach
                        @if (sizeof($rcd) > 0 && sizeof($rcd2) > 0)
                        <tr>
                            <td>TOTAL</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ $empTotal1 }}</td>
                            <td>{{ $empTotal2 }}</td>
                            <td>{{ $empTotal }}</td>
                            <td>{{ $emrTotal }}</td>
                            @if ($_key === 'sss')
                            <td>{{ $emcTotal }}</td>
                            @endif
                            <td>{{ $grandTotal }}</td>
                            <td></td>
                        </tr>
                        @endif
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
<script src="{{ asset('js/getSpecificDeductibleRecord.js') }}"></script>
@stop
