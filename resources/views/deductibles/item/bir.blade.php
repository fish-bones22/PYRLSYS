@extends('layout.master')

<?php
$_key = $details['key'];
?>

@section('title')
{{date_format(date_create($details['date']), 'M Y') }} - Withholding Tax
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
        'identifier' => isset($record->identifier['value']) ? $record->identifier['value'] : ''
    ];


}
?>

<div class="row">
    <div class="col-md-12">

        <div class="row">
            <div class="col-12 form-paper section-title" id="title">{{ date_format(date_create($details['date']), 'M Y').' to '.date_format(date_create($details['date2']), 'M Y') }} - Withholding Tax</div>
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
                            <th>Gross Pay</th>
                            <th>Exemption</th>
                            <th>Taxable <br />Income</th>
                            <th>Allowance</th>
                            <th>Exemption</th>
                            <th>Taxable <br />Income</th>
                            <th>Total Taxable <br />Income</th>
                            <th>Tax <br />Due</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $taxDueTotal = 0;
                        ?>
                        @foreach ($rcd as $key => $record)
                        <?php


                        $gp1 = isset($payrollRecord1[$key]) ?  $payrollRecord1[$key]->grossPay : 0;
                        $gp2 = isset($payrollRecord2[$key]) ?  $payrollRecord2[$key]->grossPay : 0;
                        $np1 = isset($payrollRecord1[$key]) ?  $payrollRecord1[$key]->netPay : 0;
                        $np2 = isset($payrollRecord2[$key]) ?  $payrollRecord2[$key]->netPay : 0;
                        $al1 = isset($payrollRecord1[$key]) ?  $payrollRecord1[$key]->allowance : 0;
                        $al2 = isset($payrollRecord2[$key]) ?  $payrollRecord2[$key]->allowance : 0;
                        $tp1 = isset($payrollRecord1[$key]) ?  $payrollRecord1[$key]->takeHomePay : 0;
                        $tp2 = isset($payrollRecord2[$key]) ?  $payrollRecord2[$key]->takeHomePay : 0;
                        $td1 = isset($record[$_key]['employee']) ? $record[$_key]['employee'] : 0;
                        $td2 = isset($rcd2[$key]) && isset($rcd2[$key][$_key]) ?  $rcd2[$key][$_key]['employee'] : 0;
                        $pbt1 = isset($payrollRecord1[$key]) ?  $payrollRecord1[$key]->beforeTaxPay : 0;
                        $pbt2 = isset($payrollRecord2[$key]) ?  $payrollRecord2[$key]->beforeTaxPay : 0;
                        $ex1 = isset($payrollRecord1[$key]) ?  $payrollRecord1[$key]->exemptionDetails['_TOTAL_BEFORE_TAX'] : 0;
                        $ex2 = isset($payrollRecord2[$key]) ?  $payrollRecord2[$key]->exemptionDetails['_TOTAL_BEFORE_TAX'] : 0;

                        if ($td1 + $td2 == 0) {
                            continue;
                        }

                        $taxDueTotal += $td1 + $td2;
                        ?>
                            <tr>
                                <td>{{ $record['employeeId'] }}</td>
                                <td>{{ $record['lastName'] }}</td>
                                <td>{{ $record['firstName'] }}</td>
                                <td>{{ $record['middleName'] }}</td>
                                <td>{{ $record['department'] }}</td>
                                <td>{{ isset($record[$_key]) ? $record[$_key]['identifier'] : '' }}</td>
                                <td>{{ $gp1 + $gp2 }}</td>
                                <td>{{ $ex1 + $ex2 }}</td>
                                <td>{{ $np1 + $np2 }}</td>
                                <td>{{ $al1 + $al2 }}</td>
                                <td>{{ 0 }}</td>
                                <td>{{ $pbt1 + $pbt2 }}</td>
                                <td>{{ $pbt1 + $pbt2 }}</td>
                                <td>{{ $td1 + $td2 }}</td>
                                <td>{{ isset($record['remarks']) ? $record['remarks'] : '' }}</td>
                            </tr>
                        @endforeach
                        @if ($taxDueTotal != 0 && sizeof($rcd) > 0 || sizeof($rcd2) > 0)
                        <tr>
                            <td>TOTAL</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ $taxDueTotal }}</td>
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
