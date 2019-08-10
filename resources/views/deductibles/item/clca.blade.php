@extends('layout.master')

<?php
$_key = $details['key'];
?>

@section('title')
{{date_format(date_create($details['date']), 'M Y') }} - Company Loan
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
        'identifier' => isset($record->identifier['value']) ? $record->identifier['value'] : '',
        'remarks' => $record->remarks
    ];


}
?>

<div class="row">
    <div class="col-md-12">

        <div class="row">
            <div class="col-12 form-paper section-title" id="title">{{ date_format(date_create($details['date']), 'M Y').' to '.date_format(date_create($details['date2']), 'M Y') }} - Company Loan</div>
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
                            <th>Loan Amount</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $totalLoanAmount = 0;
                        ?>
                        @foreach ($rcd as $key => $record)
                        <?php
                        $loanAmount1 = isset($record[$_key]['employee']) ? $record[$_key]['employee']  : '0';
                        $loanAmount2 = isset($rcd2[$key][$_key]['employee']) ? $rcd2[$key][$_key]['employee']  : '0';
                        $totalLoanAmount += $loanAmount1 + $loanAmount2;
                        $remarks = (isset($record[$_key]['remarks']) ? $record[$_key]['remarks'] : '').' '.(isset($rcd2[$key][$_key]['remarks']) ? $rcd2[$key][$_key]['remarks'] : '');

                        if ($loanAmount1 + $loanAmount2 === 0)
                            continue;
                        ?>
                            <tr>
                                <td>{{ $record['employeeId'] }}</td>
                                <td>{{ $record['lastName'] }}</td>
                                <td>{{ $record['firstName'] }}</td>
                                <td>{{ $record['middleName'] }}</td>
                                <td>{{ $record['department'] }}</td>
                                <td>{{ $loanAmount1 + $loanAmount2 }}</td>
                                <td>{{ $remarks }}</td>
                            </tr>
                        @endforeach
                        @if (sizeof($rcd) > 0 || sizeof($rcd2) > 0 && $totalLoanAmount > 0)
                        <tr>
                            <td>TOTAL</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ $totalLoanAmount }}</td>
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
