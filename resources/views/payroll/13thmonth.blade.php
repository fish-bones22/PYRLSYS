@extends('layout.master')

@section('title')
13th Month Pay
@stop

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-12 form-paper section-title" id="title">13<sup>th</sup> Month Pay</div>
            <div class="col-12 form-paper section-divider"></div>
        </div>
        <div class="row">
            <div class="col-7 form-paper">
                <form action="{{ route('payroll.gotodatesummary') }}" method="POST" id="setDateForm">
                    @csrf
                    @method('post')
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-paper-label">From</label>
                                <div class="input-group">
                                    @include('layout.monthselect', ['form' => 'setDateForm', 'monthSelected' => ( date_format(now()->modify('11 months ago'), 'm') ), 'name' => 'month' ])
                                    <input type="number" min="1991" max="2100" id="yearSelect" class="form-control form-control-sm" name="year" value="{{ date_format(now()->modify('11 months ago'), 'Y') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-paper-label">Until</label>
                                <div class="input-group">
                                    @include('layout.monthselect', ['form' => 'setDateForm', 'monthSelected' => ( date_format(now(), 'm') ), 'name' => 'month' ])
                                    <input type="number" min="1991" max="2100" id="yearSelect" class="form-control form-control-sm" name="year" value="{{ date_format(now(), 'Y') }}" readonly />
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-grou mb-2 float-right">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="generate()">Generate</button>
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
                                    <input type="checkbox" name="included['{{$employee->id}}']" class="employee-check" />
                                </td>
                                <td>{{ $employee->employeeId }}</td>
                                <td>{{ $employee->fullName }}</td>
                                <td>{{ $employee->current['department']['displayName'] }}</td>
                                <td>
                                    <input type="hidden" name="amount['{{$employee->id}}']" value="{{ $employee->id }}" />
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
