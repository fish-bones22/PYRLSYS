@extends('layout.master')

@section('title')
13th Month Pay
@stop

@section('content')

@if (session('error') != null)
<div class="alert alert-danger">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif
@if (session('success') != null)
<div class="alert alert-success">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif

<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-12 form-paper section-title" id="title">13<sup>th</sup> Month Pay</div>
            <div class="col-12 form-paper section-divider"></div>
        </div>
        <div class="row">
            <div class="col-7 form-paper">
                <form action="{{ route('payroll.set13thmonthpaydate') }}" method="POST" id="setDateForm">
                    @csrf
                    @method('post')
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-paper-label">From</label>
                                <div class="input-group">
                                    @include('layout.monthselect', ['id' => 'monthFrom', 'form' => 'setDateForm', 'monthSelected' => (isset($details['monthfrom']) ? $details['monthfrom'] : date_format(now()->modify('11 months ago'), 'm') ), 'name' => 'monthfrom' ])
                                    <input type="number" min="1991" max="2100" id="yearFrom" class="form-control form-control-sm" name="yearfrom" value="{{ isset($details['yearfrom']) ? $details['yearfrom'] :  date_format(now()->modify('11 months ago'), 'Y') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-paper-label">Until</label>
                                <div class="input-group">
                                    @include('layout.monthselect', ['id' => 'monthTo', 'form' => 'setDateForm', 'monthSelected' => (isset($details['monthto']) ? $details['monthto'] : date_format(now(), 'm')), 'name' => 'monthto' ])
                                    <input type="number" min="1991" max="2100" id="yearTo" class="form-control form-control-sm" name="yearto" value="{{ isset($details['yearto']) ? $details['yearto'] : date_format(now(), 'Y') }}" readonly />
                                    <button type="submit" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-right"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-grou mb-2 float-right">
                                <button id="btnGenerate" type="button" class="btn btn-secondary btn-sm" onclick="generate()">Generate</button>
                                <button form="payForm" id="btnSave" data-confirm="save" type="submit" class="btn btn-primary btn-sm" style="display: none">Save</button>
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
                            <input id="searchBox" type="search" class="form-control form-control-sm" onkeyup="filterEmployees()" />
                        </div>
                    </div>
                    <div class="col-12 mb-lg-2">
                        <div class="form-check float-right">
                            <label for="statusToggler" class="form-check-label"><input type="checkbox" class="form-check-input" id="statusToggler" onkeyup="filterStatus()" /> Show inactive employees</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-paper section-divider"></div>
            <div class="col-12 form-paper">
                <form id="payForm" action="{{ action('MiscPayableController@set13thMonthPay') }}" method="POST">
                    @csrf
                    <input type="hidden" value="{{ isset($details['startdate']) ? $details['startdate'] : '' }}" id="startDate" name="startdate" />
                    <input type="hidden" value="{{ isset($details['enddate']) ? $details['enddate'] : '' }}" id="endDate" name="enddate" />
                    <div style="overflow-x:scroll" class="mb-3">
                        <table class="table table-sm" id="payrollMasterTable">
                            <thead>
                                <tr>
                                    <th style="max-width:50px"><label for="selectAll" class="form-check-label"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()" /> All</label></th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Amount</th>
                                    <th style="display: none">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $ind = 0;
                                ?>
                                @foreach ($employees as $employee)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="included[{{$employee->id}}]" data-employee-id="{{$employee->id}}" class="employee-check" />
                                    </td>
                                    <td>{{ $employee->employeeId }}</td>
                                    <td>
                                        {{ $employee->fullName }}
                                        <input type="hidden" name="name[{{$employee->id}}]" value="{{ $employee->fullName}}" />
                                    </td>
                                    <td>
                                        {{ $employee->current['department']['displayName'] }}
                                        <input type="hidden" name="department[{{$employee->id}}]" value="{{ $employee->current['department']['value'] }}" />
                                        <input type="hidden" name="departmentName[{{$employee->id}}]" value="{{ $employee->current['department']['displayName'] }}" />
                                    </td>
                                    <td>
                                        <input type="hidden" id="amount-{{$employee->id}}" name="amount[{{$employee->id}}]" value="{{ isset($details['records'][$employee->id]) ? $details['records'][$employee->id]->amount : '' }}" />
                                        <span id="amount-display-{{$employee->id}}">{{ isset($details['records'][$employee->id]) ? $details['records'][$employee->id]->amount : '' }}</span>
                                    </td>
                                    <td style="display: none">{{ $employee->inactive ? 'Inactive' : 'Active' }}</td>
                                </tr>
                                <?php
                                $ind++;
                                ?>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
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
<script src="{{ asset('js/get13thMonthPayPage.js') }}"></script>
@stop
