@extends('layout.master')

@section('title')
Payroll Summary
@stop

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-12 form-paper section-title" id="title">Payroll Summary</div>
            <div class="col-12 form-paper section-divider"></div>
        </div>
        <div class="row">
            <div class="col-7 form-paper">
                <form action="{{ route('payroll.gotodatesummary') }}" method="POST" id="setDateForm">
                    @csrf
                    @method('post')
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
                                    @include('layout.monthselect', ['form' => 'setDateForm', 'monthSelected' => ( isset($details['month']) ? $details['month'] : date_format(now(), 'm') ), 'name' => 'month' ])
                                    <input type="number" min="1991" max="2100" id="yearSelect" class="form-control form-control-sm" name="year" value="{{ isset($details['year']) ? $details['year'] : date_format(now(), 'Y') }}" />
                                    <button type="submit" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-right"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-5 form-paper">
                <div class="row">
                    <div class="col-6">
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
                    <div class="col-6">
                        <div class="form-group float-right">
                            <label for="searchBox" class="form-paper-label">Search</label>
                            <input id="searchBox" type="search" class="form-control form-control-sm" onkeyup="searchTable()" />
                        </div>
                    </div>
                    <div class="col-12 mb-lg-2">
                        <div class="form-check">
                            <label for="statusToggler" class="form-check-label"><input type="checkbox" class="form-check-input" id="statusToggler" onkeyup="filterStatus()" /> Show inactive employees</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-paper section-divider"></div>
            <div class="col-12 form-paper">
                <div style="overflow-x:scroll" class="mb-3">
                    <table class="table table-sm" id="payrollMasterTable" style="font-size:11px;">
                        <thead>
                            <tr>
                                <th rowspan="2">ID NO.</th>
                                <th colspan="3">NAME</th>
                                <th rowspan="2" style="display: none;">DEPARTMENT</th>
                                <th rowspan="2">BASIC SALARY</th>
                                <th colspan="7">OVERTIME</th>
                                <th rowspan="2">TOTAL OVERTIME</th>
                                <th rowspan="2">Allowances</th>
                                <th rowspan="2">Special Adjustment</th>
                                <th rowspan="2">Misc. Adjustment</th>
                                <th rowspan="2">GROSS PAY</th>
                                <th colspan="7">DEDUCTIONS</th>
                                <th rowspan="2">NET PAY</th>
                                <th colspan="3">OTHER DEDUCTIONS</th>
                                <th rowspan="2">TAKE HOME PAY</th>
                                <th rowspan="2">MEAL DEDUCTIONS</th>
                                <th rowspan="2">CASH ADVANCE</th>
                                <th rowspan="2">TAKE HOME PAY</th>
                                <th style="display:none" rowspan="2">Status</th>
                            </tr>
                            <tr>
                                <th>Last Name</th>
                                <th>Given Name</th>
                                <th>Middle Name</th>
                                <th>ROT</th>
                                <th>XOT</th>
                                <th>SOT</th>
                                <th>XSOT</th>
                                <th>LHOT</th>
                                <th>XLHOT</th>
                                <th>OTHERS</th>
                                <th>W/Tax</th>
                                <th>SSS</th>
                                <th>PhilHealth</th>
                                <th>PAGIBIG</th>
                                <th>SSS Loan</th>
                                <th>PAGIBIG Loan</th>
                                <th>Others</th>
                                <th>Meal</th>
                                <th>Co. Loan</th>
                                <th>Others</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $employee)
                            <?php

                                if ($employee->inactive) continue;

                                $otherAdj = 0;
                                if (isset($summary[$employee->id]->adjustmentsDetails)) {
                                    foreach ($summary[$employee->id]->adjustmentsDetails as $key => $adj) {
                                        if (substr($key, 0, 1) === '_') continue;
                                        $otherAdj += $adj;
                                    }
                                }

                                if (isset($summary[$employee->id]->miscPay)) {
                                    foreach ($summary[$employee->id]->miscPay as $key => $adj) {
                                        $otherAdj += $adj['amount'];
                                    }
                                }

                                $otherDed = 0;
                                if (isset($summary[$employee->id]->exemptionDetails)) {
                                    foreach ($summary[$employee->id]->exemptionDetails as $key => $deductible) {
                                        if (
                                        $key === 'Withholding Tax'
                                        || $key === 'SSS'
                                        || $key === 'PhilHealth'
                                        || $key === 'PAGIBIG'
                                        || $key === 'SSS Loan'
                                        || $key === 'PAGIBIG Loan'
                                        || $key === 'Meal Deduction'
                                        || $key === 'Company Loan'
                                        || $key === 'Other Deduction'
                                        || $key === '_TOTAL_BEFORE_TAX'
                                        || $key === '_TOTAL'
                                        )
                                        continue;
                                        $otherDed += $deductible;
                                    }
                                }
                            ?>
                            <tr>
                                <td>{{ $employee->employeeId }}</td>
                                <td>{{ $employee->lastName }}</td>
                                <td>{{ $employee->firstName }}</td>
                                <td>{{ $employee->middleName }}</td>
                                <td style="display:none">{{ $employee->current['department']['displayName'] }}</td>
                                <td>{{ $summary[$employee->id]->basicPayBase }}</td>
                                <td>{{ $summary[$employee->id]->rotPay }}</td>
                                <td>{{ $summary[$employee->id]->xotPay }}</td>
                                <td>{{ $summary[$employee->id]->sotPay }}</td>
                                <td>{{ $summary[$employee->id]->xsotPay }}</td>
                                <td>{{ $summary[$employee->id]->lhotPay }}</td>
                                <td>{{ $summary[$employee->id]->xlhotPay }}</td>
                                <td>{{ $summary[$employee->id]->ndPay }}</td>
                                <td>{{ $summary[$employee->id]->otPay }}</td>
                                <td>{{ isset($summary[$employee->id]->allowance) ? $summary[$employee->id]->allowance : '0' }}</td>
                                <td></td>
                                <td>{{ $otherAdj }}</td>
                                <td>{{ $summary[$employee->id]->grossPay }}</td>
                                <td>{{ isset($summary[$employee->id]->exemptionDetails['Withholding Tax']) ? $summary[$employee->id]->exemptionDetails['Withholding Tax'] : '0' }}</td>
                                <td>{{ isset($summary[$employee->id]->exemptionDetails['SSS']) ? $summary[$employee->id]->exemptionDetails['SSS'] : '0' }}</td>
                                <td>{{ isset($summary[$employee->id]->exemptionDetails['PhilHealth']) ? $summary[$employee->id]->exemptionDetails['PhilHealth'] : '0' }}</td>
                                <td>{{ isset($summary[$employee->id]->exemptionDetails['PAGIBIG']) ? $summary[$employee->id]->exemptionDetails['PAGIBIG'] : '0' }}</td>
                                <td>{{ isset($summary[$employee->id]->exemptionDetails['SSS Loan']) ? $summary[$employee->id]->exemptionDetails['SSS Loan'] : '0' }}</td>
                                <td>{{ isset($summary[$employee->id]->exemptionDetails['PAGIBIG Loan']) ? $summary[$employee->id]->exemptionDetails['PAGIBIG Loan'] : '0' }}</td>
                                <td></td>
                                <td>{{ $summary[$employee->id]->netPay }}
                                <td>{{ isset($summary[$employee->id]->exemptionDetails['Meal Deduction']) ? $summary[$employee->id]->exemptionDetails['Meal Deduction'] : '0' }}</td>
                                <td>{{ isset($summary[$employee->id]->exemptionDetails['Company Loan']) ? $summary[$employee->id]->exemptionDetails['Company Loan'] : '0' }}</td>
                                <td>{{ $otherDed }}</td>
                                <td>{{ $summary[$employee->id]->takeHomePay }}</td>
                                <td>{{ isset($summary[$employee->id]->exemptionDetails['Meal Deduction']) ? $summary[$employee->id]->exemptionDetails['Meal Deduction'] : '0' }}</td>
                                <td>{{ isset($summary[$employee->id]->exemptionDetails['Company Loan']) ? $summary[$employee->id]->exemptionDetails['Company Loan'] : '0' }}</td>
                                <td>{{ $summary[$employee->id]->takeHomePay }}</td>
                                <td style="display:none">{{ $employee->inactive ? 'Inactive' : 'Active' }}</td>
                            </tr>
                            @endforeach
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
            <a class="btn btn-light" href="{{ action('PayrollController@index') }}">Back to List</a>
            {{-- <button type="button" class="btn btn-primary" onclick="saveAsPDF()">Save as PDF</button> --}}
            <button type="button" class="btn btn-primary" onclick="saveAsExcel()">Save as Excel</button>
        </div>
    </div>
</div>

@stop

@section('script')
<script src="{{ asset('js/payrollSummary.js') }}"></script>
@stop
