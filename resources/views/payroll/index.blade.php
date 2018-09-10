@extends('layout.master')

@section('content')

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="row">
            <div class="col-12 form-paper section-title">Employee Payroll</div>
            <div class="col-12 form-paper section-divider"></div>
        </div>
        <div class="row">
            <div class="col-12 form-paper">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="department" class="form-paper-label">department</label>
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
                <table class="table table-sm" id="payrollMasterTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Department</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $employee)
                        <tr>
                            <td>{{ $employee->fullName }}</td>
                            <td>{{ $employee->current['department']['displayName'] }}</td>
                            <td>
                                <span class="btn-group">
                                    <a role="button" href="{{ action('PayrollController@viewNow', $employee->id) }}" class="btn btn-light btn-sm">View Pay</a>
                                    <a role="button" href="{{ route('deductible.get',  ['id' => $employee->id, 'date' => date_format(now(), 'Y-m-d')]) }}" class="btn btn-light btn-sm">Set Deductibles</a>
                                    <a role="button" href="{{ route('adjustments.get',  ['id' => $employee->id, 'date' => date_format(now(), 'Y-m-d')]) }}" class="btn btn-light btn-sm">Set Adjustments</a>
                                </span>
                            </td>
                        </tr>
                        @endforeach
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
            <button class="btn btn-secondary" onclick="printAll('{{ date_format(now(), 'Y-m-d') }}')">Print All Payslip</button>
            <a role="button" href="{{ action('PayrollController@summary', date_format(now(), 'Y-m-d')) }}" class="btn btn-primary">View Payroll Summary</a>
        </div>
    </div>
</div>

@stop

@section('script')
<script src="{{ asset('js/payrollMasterlist.js') }}"></script>
<script src="{{ asset('js/printPayroll.js') }}"></script>
@stop
