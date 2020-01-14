@extends('layout.master')

@section('title')
Employees
@stop

@section('content')

@if (session('error') != null)
<div class="alert alert-danger">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif
@if (session('success') != null)
<div class="alert alert-success">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif

<div class="row">
    <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 form-paper section-title">Employees
        <span class="float-right">
            <a role="button" href="employee/new" class="btn btn-link btn-clipping">New Employee</a>
        </span>
    </div>
    <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 form-paper section-divider"></div>
    <div class="col-lg-3 offset-lg-2 col-md-4 offset-md-1 col-sm-4 form-paper">
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
    <div class="col-lg-3 col-md-3 col-sm-4 form-paper">
        <div class="form-group">
            <label for="searchBox" class="form-paper-label">Search</label>
            <input id="searchBox" class="form-control form-control-sm" type="search" onkeyup="filterEmployees()" />
        </div>
    </div>
    <div class="col-lg-2 col-md-3 col-sm-4 form-paper">
        <div class="form-group">
            <label for="tinFilter" class="form-paper-label">TIN Filter</label>
            <select id="tinFilter" class="form-control form-control-sm" onchange="filterTIN()">
                <option value="all">All</option>
                <option value="tin">With TIN</option>
                <option value="notin">Without TIN</option>
            </select>
        </div>
    </div>
    <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 form-paper section-divider"></div>
    <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 form-paper">
        <div style="overflow-x: auto">
            <table class="table table-sm" id="employeesTable">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th style="display:none">Status</th>
                        <th style="display:none">TIN</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $emp)
                    <tr>
                        <td>{!! $emp->employeeId != null ? $emp->employeeId : '<i class="small text-muted">No ID</i>' !!}</td>
                        <td>{{ $emp->fullName }}</td>
                        <td>{{ $emp->current['department']['displayName'] }}</td>
                        <td style="display:none">{{ $emp->inactive ? 'Inactive' : 'Active' }}</td>
                        <td style="display:none">{{ isset($emp->deductibles['tin']) ? $emp->deductibles['tin']['value'] : '' }}</td>
                        <td>
                            <div class="btn-group">
                                <a class="btn btn-sm btn-light" href="{{ action('EmployeeController@view', ['id' => $emp->id ]) }}">View</a>
                                <form action="{{ route('employee.delete', $emp->id) }}" method="post">
                                    @csrf
                                    @method('post')
                                    <input class="btn btn-sm btn-secondary" data-confirm="delete" type="submit" value="Delete" />
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>



<div class="row" style="display:none">
    <div class="col">
        <div id="title">Employees Masterlist</div>
        <table id="employeesSummaryTable" style="font-size:0.5em;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Timecard</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Sex</th>
                    <th>Civil Status</th>
                    <th>Birthday</th>
                    <th>Spouse</th>
                    <th>Dependents</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th>Phone Number 1</th>
                    <th>Phone Number 2</th>
                    <th>Emergency Contact Name</th>
                    <th>Emergency Contact Number</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Employment Type</th>
                    <th>Date Hired</th>
                    <th>Date End</th>
                    <th>Contract Status</th>
                    <th>SSS</th>
                    <th>TIN</th>
                    <th>PhilHealth</th>
                    <th>PAGIBIG</th>
                    <th>Type of Payment</th>
                    <th>Mode of Payment</th>
                    <th>Rate Basis</th>
                    <th>Rate</th>
                    <th>Allowance</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Break</th>
                    <th>Change Shift Schedule</th>
                    <th>Until</th>
                    <th>Pending Memos</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employees as $employee)
                <tr>
                    <td>{{ $employee->employeeId }}</td>
                    <td>{{ isset($employee->current['timecard']) ? $employee->current['timecard'] : '' }}</td>
                    <td>{{ $employee->lastName }}</td>
                    <td>{{ $employee->firstName }}</td>
                    <td>{{ $employee->middleName }}</td>
                    <td>{{ $employee->sex }}</td>
                    <td>{{ isset($employee->details['civilstatus']['value']) ? $employee->details['civilstatus']['value'] : '' }}</td>
                    <td>{{ key_exists('birthday', $employee->details) ? $employee->details['birthday']['value'] : '' }}</td>
                    <td>{{ isset($employee->details['spouse']) ? $employee->details['spouse'][0]['firstname']['value'].' '.$employee->details['spouse'][0]['middlename']['value'].' '.$employee->details['spouse'][0]['lastname']['value'] : '' }}</td>
                        <?php
                        $dependents = '';
                        if (isset($employee->details['dependent'])) {
                            foreach ($employee->details['dependent'] as $value) {
                                $dependents .= $value['firstname']['value'].' '.(isset($value['middlename']['value']) ? $value['middlename']['value'] : '').' '.$value['lastname']['value'].' - '.(isset($value['middlename']['value']) ? $value['relationship']['value'] : '' ).', ';
                            }
                            $dependents = strlen($dependents) > 2 ? substr($dependents, 0, strlen($dependents) - 2) : '';
                        }
                        ?>
                    <td>{{ $dependents }}</td>
                    <td>{{  $employee->details != null && key_exists('presentaddress', $employee->details) ? $employee->details['presentaddress']['value']: '' }}</td>
                    <td>{{  $employee->details != null && key_exists('emailaddress', $employee->details) ? $employee->details['emailaddress']['value']: '' }}</td>
                    <td>{{  $employee->details != null && key_exists('phonenumber1', $employee->details) ? $employee->details['phonenumber1']['value']: '' }}</td>
                    <td>{{  $employee->details != null && key_exists('phonenumber2', $employee->details) ? $employee->details['phonenumber2']['value']: '' }}</td>
                    <td>{{  $employee->details != null && key_exists('emergencyname', $employee->details) ? $employee->details['emergencyname']['value']: '' }}</td>
                    <td>{{  $employee->details != null && key_exists('emergencyphone', $employee->details) ? $employee->details['emergencyphone']['value']: '' }}</td>
                    <td>{{ isset($employee->current['department']) ? $employee->current['department']['displayName'] : '' }}</td>
                    <td>{{ isset($employee->current['position']) ? $employee->current['position'] : '' }}</td>
                    <td>{{ isset($employee->current['employmenttype']) ? $employee->current['employmenttype']['displayName'] : '' }}</td>
                    <td>{{ isset($employee->current['datestarted']) ? date_format(date_create($employee->current['datestarted']), 'Y-m-d') : '' }}</td>
                    <td>{{ isset($employee->current['datetransfered']) ? date_format(date_create($employee->current['datetransfered']), 'Y-m-d') : '' }}</td>
                    <td>{{ isset($employee->current['contractstatus']) ? $employee->current['contractstatus']['displayName'] : '' }}</td>
                    <td>{{ isset($employee->deductibles['sss']) ? $employee->deductibles['sss']['value'] : '' }}</td>
                    <td>{{ isset($employee->deductibles['tin']) ? $employee->deductibles['tin']['value'] : '' }}</td>
                    <td>{{ isset($employee->deductibles['philhealth']) ? $employee->deductibles['philhealth']['value'] : '' }}</td>
                    <td>{{ isset($employee->deductibles['pagibig']) ? $employee->deductibles['pagibig']['value'] : '' }}</td>
                    <td>{{ isset($employee->current['paymenttype']) ? $employee->current['paymenttype']['displayName'] : '' }}</td>
                    <td>{{ isset($employee->payTable['paymentmode']) ? $employee->payTable['paymentmode']['displayName'] : '' }}</td>
                    <td>{{ isset($employee->payTable['ratebasis']) ? $employee->payTable['ratebasis'] : '' }}</td>
                    <td>{{ isset($employee->payTable['rate']) ? $employee->payTable['rate'] : '' }}</td>
                    <td>{{ isset($employee->payTable['allowance']) ? $employee->payTable['allowance'] : '' }}</td>
                    <td>{{ $employee->timeTable != null && isset($employee->timeTable['timein']) ? date_format(date_create($employee->timeTable['timein']), 'h:i A') : '' }}</td>
                    <td>{{ $employee->timeTable != null && isset($employee->timeTable['timeout']) ? date_format(date_create($employee->timeTable['timeout']), 'h:i A') : '' }}</td>
                    <td>{{ $employee->timeTable != null && isset($employee->timeTable['break']) && $employee->timeTable['break']*1 > 0 ? $employee->timeTable['break'] : '' }}</td>
                    <td>{{ $employee->timeTable != null && isset($employee->timeTable['startdate']) ? date_format(date_create($employee->timeTable['startdate']), 'Y-m-d') : '' }}</td>
                    <td>{{ $employee->timeTable != null && isset($employee->timeTable['enddate']) ? date_format(date_create($employee->timeTable['enddate']), 'Y-m-d') : '' }}</td>
                    <td>{{ isset($employee->details['numberofmemo']) ? $employee->details['numberofmemo']['value'] : '' }}</td>
                    <td>{{ isset($employee->details['remarks']) ? $employee->details['remarks']['value'] : '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="m-4">&nbsp;</div>
<div class="fixed-bottom btn-container m-4">
    <div class="float-right">
        <div class="btn-group">
            <button id="toggleInactive" type="button" class="btn btn-secondary">Inactive Employees</button>
            <form id="deleteInactive" action="{{ route('employee.deleteallinactive') }}" method="POST" style="display:none">
                @csrf
                @method('post')
                <input type="submit" class="btn btn-secondary" data-confirm="delete all inactive" value="Delete All Inactive"/>
            </form>
            <form id="deleteAll" action="{{ route('employee.deleteall') }}" method="POST">
                @csrf
                @method('post')
                <input type="submit" class="btn btn-secondary" data-confirm="delete all" value="Delete All"/>
            </form>
            {{-- <button type="button" class="btn btn-primary" onclick="saveAsPDF()">Save as PDF</button> --}}
            <button type="button" class="btn btn-primary" onclick="saveAsExcel()">Save as Excel</button>
        </div>
    </div>
</div>

@stop
@section('script')
<script src="{{ asset('js/employeesPage.js') }}"></script>
@stop

