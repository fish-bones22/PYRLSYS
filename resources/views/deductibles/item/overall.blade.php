@extends('layout.master')

<?php
$_key = $details['key'];
$_keySS = 'sss';
$_keyPH = 'philhealth';
$_keyPI = 'pagibig';
$_keyWT = 'tin';
?>

@section('title')
{{date_format(date_create($details['date']), 'M Y').' to '.date_format(date_create($details['date2']), 'M Y') }} - Deductions
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
            'department' => $record->employee['department'],
            'inactive' => $record->employee['inactive']
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
    if (!isset($rcd2[$record->employee['id']])) {
        $rcd2[$record->employee['id']] = [
            'employeeId' => $record->employee['employeeId'],
            'lastName' => $record->employee['lastname'],
            'firstName' => $record->employee['firstname'],
            'middleName' => $record->employee['middlename'],
            'basicsalary' => $record->employee['basicsalary'],
            'department' => $record->employee['department'],
            'inactive' => $record->employee['inactive']
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
            <div class="col-12 form-paper section-title" id="title">{{ date_format(date_create($details['date']), 'M Y').' to '.date_format(date_create($details['date2']), 'M Y') }} - Deductions</div>
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
                        <div class="col-12 mb-2">
                            <div class="form-check float-right">
                                <label for="statusToggler" class="form-check-label"><input type="checkbox" class="form-check-input" id="statusToggler" onkeyup="filterStatus()" /> Show inactive employees</label>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="col-12 form-paper section-divider"></div>
        </div>

        <div class="row">
            <div class="col-12 form-paper">
                <div style="overflow-x:scroll" class="mb-3">
                    <table class="table table-sm" id="deductiblesTable" style="font-size:11px;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Department</th>
                                <th>SSS Emp</th>
                                <th>SSS Emr</th>
                                <th>SSS Emc</th>
                                <th>SSS Total</th>
                                <th>PhilHealth Emp</th>
                                <th>PhilHealth Emr</th>
                                <th>PhilHealth Total</th>
                                <th>PAGIBIG Emp</th>
                                <th>PAGIBIG Emr</th>
                                <th>PAGIBIG Total</th>
                                <th>Withholding Tax</th>
                                <th style="display:none">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sssEmp = 0;
                            $sssEmr = 0;
                            $sssEmc = 0;
                            $sss = 0;

                            $philhealthEmp = 0;
                            $philhealthEmr = 0;
                            $philhealth = 0;

                            $pagibigEmp = 0;
                            $pagibigEmr = 0;
                            $pagibig = 0;

                            $tax = 0;

                            $rcdToUse = $rcd;
                            // Set array to iterate
                            if (!array_filter($rcd)) {
                                $rcdToUse = $rcd2;
                            }
                            ?>
                            @foreach ($rcdToUse as $key => $record)
                            <?php
                            $sssEmp1 = isset($rcd[$key][$_keySS]['employee']) ? $rcd[$key][$_keySS]['employee']  : '0';
                            $sssEmp2 = isset($rcd2[$key][$_keySS]['employee']) ? $rcd2[$key][$_keySS]['employee']  : '0';
                            $sssEmr1 = isset($rcd[$key][$_keySS]['employer']) ? $rcd[$key][$_keySS]['employer']  : '0';
                            $sssEmr2 = isset($rcd2[$key][$_keySS]['employer']) ? $rcd2[$key][$_keySS]['employer']  : '0';
                            $sssEmc1 = isset($rcd[$key][$_keySS]['subamount']) ? $rcd[$key][$_keySS]['subamount']  : '0';
                            $sssEmc2 = isset($rcd2[$key][$_keySS]['subamount2']) ? $rcd2[$key][$_keySS]['subamount2']  : '0';

                            $sssEmp += $sssEmp1 + $sssEmp2;
                            $sssEmr += $sssEmr1 + $sssEmr2;
                            $sssEmc += $sssEmc1 + $sssEmc2;

                            $sss += $sssEmp + $sssEmr + $sssEmc;

                            $philhealthEmp1 = isset($rcd[$key][$_keyPH]['employee']) ? $rcd[$key][$_keyPH]['employee']  : '0';
                            $philhealthEmp2 = isset($rcd2[$key][$_keyPH]['employee']) ? $rcd2[$key][$_keyPH]['employee']  : '0';

                            $philhealthEmr1 = isset($rcd[$key][$_keyPH]['employer']) ? $rcd[$key][$_keyPH]['employer']  : '0';
                            $philhealthEmr2 = isset($rcd2[$key][$_keyPH]['employer']) ? $rcd2[$key][$_keyPH]['employer']  : '0';

                            $philhealthEmp += $philhealthEmp1 + $philhealthEmp2;
                            $philhealthEmr += $philhealthEmr1 + $philhealthEmr2;

                            $philhealth += $philhealthEmp + $philhealthEmr;

                            $pagibigEmp1 = isset($rcd[$key][$_keyPI]['employee']) ? $rcd[$key][$_keyPI]['employee']  : '0';
                            $pagibigEmp2 = isset($rcd2[$key][$_keyPI]['employee']) ? $rcd2[$key][$_keyPI]['employee']  : '0';

                            $pagibigEmr1 = isset($rcd[$key][$_keyPI]['employee']) ? $rcd[$key][$_keyPI]['employee']  : '0';
                            $pagibigEmr2 = isset($rcd2[$key][$_keyPI]['employee']) ? $rcd2[$key][$_keyPI]['employee']  : '0';

                            $pagibigEmp += $pagibigEmp1 + $pagibigEmp2;
                            $pagibigEmr += $pagibigEmr1 + $pagibigEmr2;
                            $pagibig += $pagibigEmp + $pagibigEmr;

                            $tax1 = isset($rcd[$key][$_keyWT]['employee']) ? $rcd[$key][$_keyWT]['employee']  : '0';
                            $tax2 = isset($rcd2[$key][$_keyWT]['employee']) ? $rcd2[$key][$_keyWT]['employee']  : '0';


                            $tax += $tax1 + $tax2;

                            ?>
                                <tr>
                                    <td>{{ $record['employeeId'] }}</td>
                                    <td>{{ $record['lastName'] }}</td>
                                    <td>{{ $record['firstName'] }}</td>
                                    <td>{{ $record['middleName'] }}</td>
                                    <td>{{ $record['department'] }}</td>
                                    <td>{{ $sssEmp1 + $sssEmp2 }}</td>
                                    <td>{{ $sssEmr1 + $sssEmr2 }}</td>
                                    <td>{{ $sssEmc1 + $sssEmc2 }}</td>
                                    <td>{{ $sssEmp1 + $sssEmp2 + $sssEmr1 + $sssEmr2 + $sssEmc1 + $sssEmc2 }}</td>
                                    <td>{{ $philhealthEmp1 + $philhealthEmp2 }}</td>
                                    <td>{{ $philhealthEmr1 + $philhealthEmr2 }}</td>
                                    <td>{{ $philhealthEmp1 + $philhealthEmr1 + $philhealthEmp2 + $philhealthEmr2 }}</td>
                                    <td>{{ $pagibigEmp1 + $pagibigEmp2 }}</td>
                                    <td>{{ $pagibigEmr1 + $pagibigEmr2 }}</td>
                                    <td>{{ $pagibigEmp1 + $pagibigEmp2 + $pagibigEmr1 + $pagibigEmr2  }}</td>
                                    <td>{{ $tax1 + $tax2 }}</td>
                                    <td style="display:none">{{ $record['inactive'] ? 'Inactive' : 'Active' }}</td>
                                </tr>
                            @endforeach
                            @if (sizeof($rcd) > 0 || sizeof($rcd2) > 0 && $sss > 0)
                            <tr>
                                <td>TOTAL</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{ $sssEmp }}</td>
                                <td>{{ $sssEmr }}</td>
                                <td>{{ $sssEmc }}</td>
                                <td>{{ $sss }}</td>
                                <td>{{ $philhealthEmp }}</td>
                                <td>{{ $philhealthEmr }}</td>
                                <td>{{ $philhealth }}</td>
                                <td>{{ $pagibigEmp }}</td>
                                <td>{{ $pagibigEmr }}</td>
                                <td>{{ $pagibig }}</td>
                                <td>{{ $tax }}</td>
                                <td style="display:none"></td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
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
