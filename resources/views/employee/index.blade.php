@extends('layout.master')

@section('title')
Employees
@stop

@section('content')

<div class="row">
    <div class="col-md-6 offset-md-3 form-paper section-title">Employees</div>
    <div class="col-md-6 offset-md-3 form-paper section-divider"></div>
    <div class="col-md-2 offset-md-3 form-paper">
        <div class="form-group mt-3">
            <a role="button" href="employee/new" class="btn btn-sm btn-block btn-light">New Employee</a>
        </div>
    </div>
    <div class="col-md-2 form-paper">
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
    <div class="col-md-2 form-paper">
        <div class="form-group">
            <label for="searchBox" class="form-paper-label">Search</label>
            <input id="searchBox" class="form-control form-control-sm" type="search" onkeyup="filterEmployees()" />
        </div>
    </div>
    <div class="col-md-6 offset-md-3 form-paper section-divider"></div>
    <div class="col-md-6 offset-md-3 form-paper">
        <table class="table table-sm" id="employeesTable">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $emp)
                <tr>
                    <td>{!! $emp->employeeId != null ? $emp->employeeId : '<i class="small text-muted">No ID</i>' !!}</td>
                    <td>{{ $emp->fullName }}</td>
                    <td>{{ $emp->current['department']['displayName'] }}</td>
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
                    <th>Spouse</th>
                    <th>Dependents</th>
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
                    <th>Rate</th>
                    <th>Allowance</th>
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
                    <td>{{ isset($employee->details['spouse']) ? $employee->details['spouse'][0]['firstname']['value'].' '.$employee->details['spouse'][0]['middlename']['value'].' '.$employee->details['spouse'][0]['lastname']['value'] : '' }}</td>
                        <?php
                        $dependents = '';
                        if (isset($employee->details['dependents'])) {
                            foreach ($employee->details['dependents'] as $key => $value) {
                                $dependents = $dependents.$value['firstname']['value'].' '.$value['middlename']['value'].' '.$value['lastname']['value'].', ';
                            }
                            $dependents = substr($dependents, sizeof($dependents) - 1);
                        }
                        ?>
                    <td>{{ $dependents }}</td>
                    <td>{{ isset($employee->current['department']) ? $employee->current['department']['displayName'] : '' }}</td>
                    <td>{{ isset($employee->current['position']) ? $employee->current['position'] : '' }}</td>
                    <td>{{ isset($employee->current['employmenttype']) ? $employee->current['employmenttype']['displayName'] : '' }}</td>
                    <td>{{ isset($employee->current['datestarted']) ? date_format(date_create($employee->current['datestarted']), 'Y-m-d') : '' }}</td>
                    <td>{{ isset($employee->current['datetransfered']) ? date_format(date_create($employee->current['datetransfered']), 'Y-m-d') : '' }}</td>
                    <td>{{ isset($employee->current['contractstatus']) ? $employee->current['contractstatus']['displayName'] : '' }}</td>
                    <td>{{ isset($employee->deductibles['sss']) ? $employee->deductibles['sss'] : '' }}</td>
                    <td>{{ isset($employee->deductibles['tin']) ? $employee->deductibles['tin'] : '' }}</td>
                    <td>{{ isset($employee->deductibles['philhealth']) ? $employee->deductibles['philhealth'] : '' }}</td>
                    <td>{{ isset($employee->deductibles['pagibig']) ? $employee->deductibles['pagibig'] : '' }}</td>
                    <td>{{ isset($employee->current['paymenttype']) ? $employee->current['paymenttype']['displayName'] : '' }}</td>
                    <td>{{ isset($employee->current['paymentmode']) ? $employee->current['paymentmode']['displayName'] : '' }}</td>
                    <td>{{ isset($employee->current['rate']) ? $employee->current['rate'] : '' }}</td>
                    <td>{{ isset($employee->current['allowance']) ? $employee->current['allowance'] : '' }}</td>
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
            <form action="{{ route('employee.deleteall') }}" method="POST">
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

