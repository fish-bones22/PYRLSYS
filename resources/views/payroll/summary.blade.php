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
                                    @include('layout.monthselect', ['form' => 'setDateForm', 'monthSelected' => ( isset($details['month']) ? $details['month'] : date_format(now(), 'm') ) ])
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
                            <label for="dapartment" class="form-paper-label">Dapartment</label>
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
                                <th>ID</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Department</th>
                                <th>Basic Pay</th>
                                <th>Adjustments</th>
                                <th>Reg. OT</th>
                                <th>Adj. OT</th>
                                <th>ND</th>
                                <th>Gross Pay</th>
                                <th>SSS Premium</th>
                                <th>PhilHealth Premium</th>
                                <th>PAGIBIG Premium</th>
                                <th>W/Tax</th>
                                <th>SSS Loan</th>
                                <th>PAGIBIG Loan</th>
                                <th>CO. CA</th>
                                <th>Adjustments</th>
                                <th>Net Pay</th>
                                <th>Monthly Allowance</th>
                                <th>Meal Allowance</th>
                                <th>Differential</th>
                                <th>Gross Net Pay</th>
                                <th>Meal Ded.</th>
                                <th>Other Ded.</th>
                                <th>Take Home Pay</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $employee)
                            <tr>
                                <td>{{ $employee->employeeId }}</td>
                                <td>{{ $employee->lastName }}</td>
                                <td>{{ $employee->firstName }}</td>
                                <td>{{ $employee->middleName }}</td>
                                <td>{{ $employee->current['department']['displayName'] }}</td>
                                <td>{{ $summary[$employee->id]->basicPay }}</td>
                                <td>0</td>
                                <td>{{ $summary[$employee->id]->rotPay }}</td>
                                <td>{{ $summary[$employee->id]->otPay - $summary[$employee->id]->rotPay }}</td>
                                <td>{{ isset($summary[$employee->id]->otDetails['ndrate']) ? $summary[$employee->id]->otDetails['ndrate'] : '' }}</td>
                                <td>{{ $summary[$employee->id]->grossPay }}</td>
                                <td>{{ isset($summary[$employee->id]->exemptionDetails['SSS']) ? $summary[$employee->id]->exemptionDetails['SSS'] : '0' }}</td>
                                <td>{{ isset($summary[$employee->id]->exemptionDetails['PhilHealth']) ? $summary[$employee->id]->exemptionDetails['PhilHealth'] : '0' }}</td>
                                <td>{{ isset($summary[$employee->id]->exemptionDetails['PAGIBIG']) ? $summary[$employee->id]->exemptionDetails['PAGIBIG'] : '0' }}</td>
                                <td>{{ isset($summary[$employee->id]->exemptionDetails['Withholding Tax']) ? $summary[$employee->id]->exemptionDetails['Withholding Tax'] : '0' }}</td>
                                <td>{{ isset($summary[$employee->id]->exemptionDetails['SSS Loan']) ? $summary[$employee->id]->exemptionDetails['SSS Loan'] : '0' }}</td>
                                <td>{{ isset($summary[$employee->id]->exemptionDetails['PAGIBIG Loan']) ? $summary[$employee->id]->exemptionDetails['PAGIBIG Loan'] : '0' }}</td>
                                <td>{{ isset($summary[$employee->id]->exemptionDetails['Company Loan']) ? $summary[$employee->id]->exemptionDetails['Company Loan'] : '0' }}</td>
                                <td>{{ 0 }}</td>
                                <td>{{ $summary[$employee->id]->netPay }}</td>
                                <td>{{ isset($summary[$employee->id]->adjustmentsDetails['Monthly Allowance']) ? $summary[$employee->id]->adjustmentsDetails['Monthly Allowance'] : '0' }}</td>
                                <td>{{ isset($summary[$employee->id]->adjustmentsDetails['Meal Allowance']) ? $summary[$employee->id]->adjustmentsDetails['Meal Allowance'] : '0' }}</td>
                                <td>0</td>
                                <td>{{ $summary[$employee->id]->takeHomePay }}</td>
                                <td>{{ isset($summary[$employee->id]->exemptionDetails['Meal Deduction']) ? $summary[$employee->id]->exemptionDetails['Meal Deduction'] : '0' }}</td>
                                <td>{{ isset($summary[$employee->id]->exemptionDetails['Other Deduction']) ? $summary[$employee->id]->exemptionDetails['Other Deduction'] : '0' }}</td>
                                <td>{{ $summary[$employee->id]->takeHomePay }}</td>
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
<script src="{{ asset('js/payrollMasterlist.js') }}"></script>
@stop
