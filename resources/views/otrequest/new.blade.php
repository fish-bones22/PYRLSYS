@extends('layout.master')

@section('title')
New OT Request
@stop

@section('content')

<div class="row">
    <div class="col-lg-8 offset-lg-2">

        <form action="{{ action('OtRequestController@add') }}" method="POST" >

            @csrf
            @method('post')
            <div class="row">
                <div class="col-12 form-paper text-center"><div class="display-4">New Request</div></div>
                <div class="col-12 form-paper section-divider"></div>
            </div>

            <div class="row">
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="date" class="form-paper-label">Date</label>
                        <input id="date" class="form-control" name="date" type="date" required />
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="department" class="form-paper-label" >Department</label>
                        <select id="department" class="form-control" name="department" onchange="getEmployeesOnDepartment()" required>
                            <option value=""></option>
                            <option value="0">All</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>

            <input type="hidden" id="employee-index" value="1" />
            <div class="row">
                <div class="col-12 form-paper section-title">Employee</div>
                <div class="col-12 form-paper section-divider"></div>
            </div>

            <div class="row employee-0">
                <div class="form-paper section-delete">
                    <button class="close" type="button" data-index="0" onclick="deleteRow(this, 'employee')" tabindex="-1">&times;</button>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="employee[0]" class="form-paper-label">Employee Name</label>
                        <select id="employee[0]" type="text" name="employee_name[0]" class="form-control employee-list"></select>
                        <input type="hidden" name="employee_id[0]" />
                    </div>
                </div>
                <div class="col-3 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Employee ID</label>
                        <div class="form-paper-display employee-id"><i class="text-muted small">ID</i></div>
                    </div>
                </div>
                <div class="col-3 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Time Card</label>
                        <div class="form-paper-display timecard"><i class="text-muted small">Time card</i></div>
                    </div>
                </div>
                <div class="col-md-2 col-4 form-paper">
                    <div class="form-group">
                        <label for="allowedHours[0]" class="form-paper-label">OT Hrs Allowed</label>
                        <input id="allowedHours[0]" class="form-control" name="allowed_hours[0]" type="number" step="0.5" />
                    </div>
                </div>
                <div class="col-md-3 col-4 form-paper">
                    <div class="form-group">
                        <label for="from[0]" class="form-paper-label">From</label>
                        <input id="from[0]" class="form-control" name="from[0]" type="time" />
                    </div>
                </div>
                <div class="col-md-3 col-4 form-paper">
                    <div class="form-group">
                        <label for="to[0]" class="form-paper-label">To</label>
                        <input id="to[0]" class="form-control" name="to[0]" type="time" />
                    </div>
                </div>
                <div class="col-md-4 form-paper">
                    <div class="form-group">
                        <label for="reason[0]" class="form-paper-label">Reason</label>
                        <input id="reason[0]" class="form-control" name="reason[0]" type="text" />
                    </div>
                </div>
                <div class="col-12 form-paper section-divider">&nbsp;</div>
            </div>

            <div class="row addContainer">
                <div class="col-12 form-paper">
                    <button class="btn btn-link" type="button" onclick="createNewRow(this, 'employee')">Add Employee</button>
                </div>
            </div>

            <div class="m-4">&nbsp;</div>
            <div class="fixed-bottom btn-container m-4">
                <div class="float-right">
                    <div class="btn-group">
                        <a class="btn btn-light" href="{{ action('OtRequestController@index') }}">Back to List</a>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <input type="submit" class="btn btn-primary" value="Save"/>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

@stop

@section('script')
<script src="{{ asset('js/dynamicAddAndDelete.js') }}"></script>
<script src="{{ asset('js/otRequestPage.js') }}"></script>
@stop



