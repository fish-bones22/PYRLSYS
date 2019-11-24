@extends('layout.master')

@section('title')
{{ $employee->fullName != '' ? $employee->fullName : 'New Employee' }}
@stop

@section('content')

@if (session('error') != null)
<div class="alert alert-danger">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif
@if (session('success') != null)
<div class="alert alert-success">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif
<div class="">
    <div class="col-md-10 offset-1">

    <div class="row align-items-end">
        <div class="col-sm-9">
            <div class="display-4">{{ $employee->fullName != '' ? $employee->fullName : 'New Employee' }}</div>
        </div>
        <div class="col-sm-3 pb-2">
            <div class="current-image float-md-right mx-auto">
                {{-- Use image select modal if updating --}}
                @if ($employee->id != 0)
                <button type="button" class="btn btn-dark btn-change-image" data-toggle="modal" data-target="#selectPictureModal"><i class="fa fa-pencil"></i></button>
                {{-- Use file input if new employee --}}
                @else
                <input form="employeeForm" type="file" class="form-control-file btn-change-image" accept="image/*" name="new_image_file" />
                @endif
                <img id="primaryImage" src="{{ $employee->currentPicture == null ? asset('img/anom.png') : asset('storage/'.$employee->currentPicture['location'].$employee->currentPicture['filename']) }}" class="img-fluid" />
            </div>
        </div>
    </div>


        <form id="employeeForm" action="{{ action('EmployeeController@update', $employee->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('post')
            <input type="hidden" name="id" value="{{ $employee->id }}" />

            <div class="row">
                <div class="col form-paper section-title">Personal Information</div>
                <div class="col-12 form-paper section-divider"></div>
            </div>
            <div class="row">
                <div class="col-md-3 form-paper">
                    <div class="form-group">
                        <label for="employeeId" class="form-paper-label">Employee ID:</label>
                        <input id="employeeId" name="employee_id" type="text" class="form-control" value="{{ $employee->employeeId }}" required/>
                    </div>
                </div>
                <div class="col-md-3 col-6 form-paper">
                    <div class="form-group">
                        <label for="sex" class="form-paper-label">Sex:</label>
                        <select id="sex" name="sex" class="form-control" value="{{ $employee->sex }}">
                            <option value="m" {{ $employee->sex == 'm' ? 'selected' : '' }}>Male</option>
                            <option value="f" {{ $employee->sex == 'f' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-6 form-paper">
                    <div class="form-group">
                        <label for="civilStatus" class="form-paper-label">Civil Status:</label>
                        <select id="civilStatus" name="civil_status" class="form-control" value="{{ key_exists('civilstatus', $employee->details) ? $employee->details['civilstatus']['value'] : '' }}">
                            <option value="single" {{ key_exists('civilstatus', $employee->details) && $employee->details['civilstatus']['value'] == 'single' ? 'selected' : ''}} >Single</option>
                            <option value="married" {{ key_exists('civilstatus', $employee->details) && $employee->details['civilstatus']['value'] == 'married' ? 'selected' : ''}} >Married</option>
                            <option value="widow" {{ key_exists('civilstatus', $employee->details) && $employee->details['civilstatus']['value'] == 'widow' ? 'selected' : ''}} >Widow</option>
                            <option value="separated"  {{ key_exists('civilstatus', $employee->details) && $employee->details['civilstatus']['value'] == 'separated' ? 'selected' : ''}} >Legally Separated</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-12 form-paper">
                    <div class="form-group">
                        <label for="birthday" class="form-paper-label">Birthday:</label>
                        <input type="date" name="birthday" class="form-control" id="birthday" value="{{ key_exists('birthday', $employee->details) ? $employee->details['birthday']['value'] : '' }}" required />
                    </div>
                </div>
                <div class="col-md-12 form-paper">
                    <div class="row">
                        <div class="col-12 section-label">Name</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group display-block-inline">
                                <label for="lastname" class="form-paper-label">Last name:</label>
                                <input id="lastname" name="last_name" type="text" class="form-control" value="{{ $employee->lastName }}"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group display-block-inline">
                                <label for="firstname" class="form-paper-label">First name:</label>
                                <input id="firstname" name="first_name" type="text" class="form-control" value="{{ $employee->firstName }}"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group display-block-inline">
                                <label for="middlename" class="form-paper-label">Middle name:</label>
                                <input id="middlename" name="middle_name" type="text" class="form-control" value="{{ $employee->middleName }}"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>


            <div class="row">

                <div class="col-md-12 form-paper">
                    <div class="row">
                        <div class="col-12 section-label">Spouse (Maiden) Name</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="spouseLastName" class="form-paper-label">Last name:</label>
                                <input id="spouseLastName" name="spouse_last_name[0]" type="text" class="form-control" value="{{ key_exists('spouse', $employee->details) && isset($employee->details['spouse'][0]['lastname']) ? $employee->details['spouse'][0]['lastname']['value'] : '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="spouseFirstName" class="form-paper-label">First name:</label>
                                <input id="spouseFirstName" name="spouse_first_name[0]" type="text" class="form-control" value="{{ key_exists('spouse', $employee->details) && isset($employee->details['spouse'][0]['firstname']) ? $employee->details['spouse'][0]['firstname']['value'] : '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="spouseMiddleName" class="form-paper-label">Middle name:</label>
                                <input id="spouseMiddleName" name="spouse_middle_name[0]" type="text" class="form-control" value="{{ key_exists('spouse', $employee->details) && isset($employee->details['spouse'][0]['middlename']) ? $employee->details['spouse'][0]['middlename']['value'] : '' }}" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>

            <?php

            // To ensure empty Dependent fields appear,
            // iterate at least one row even if $details['dependent'] is empty

            $limit = 1; // Store the number of dependents or use 1 if there are none.
            // If there is/are dependent/s on the array, use its size instead of the default 1.
            if (key_exists('dependent', $employee->details) && is_array($employee->details['dependent']) && sizeof($employee->details['dependent']) > 0) {
                $limit = sizeof($employee->details['dependent']);
            }

            ?>

            <input type="hidden" id="dependent-index" value="{{ $limit }}" />

            {{-- Use the $limit variable for looping --}}

            <div class="row">
                <div class="col-12 form-paper section-title">Dependent</div>
                <div class="col-12 form-paper section-divider"></div>
            </div>
            @for ($i = 0; $i < $limit; $i++)
            <div class="row dependent-{{$i}}">

                <div class="col-12">
                    <div class="form-paper section-delete">
                        <button type="button" class="close text-muted" data-index="{{$i}}" onclick="deleteRow(this, 'dependent')" tabindex="-1">&times;</button>
                    </div>
                </div>
                <div class="col-md-8 form-paper">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="dependentLastName[{{$i}}]" class="form-paper-label">Last name:</label>
                                <input id="dependentLastName[{{$i}}]" name="dependent_last_name[{{$i}}]" type="text" class="form-control" value="{{ key_exists('dependent', $employee->details) && isset($employee->details['dependent'][$i]['lastname']) ? $employee->details['dependent'][$i]['lastname']['value'] : '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="dependentFirstName[{{$i}}]" class="form-paper-label">First name:</label>
                                <input id="dependentFirstName[{{$i}}]" name="dependent_first_name[{{$i}}]" type="text" class="form-control" value="{{ key_exists('dependent', $employee->details) && isset($employee->details['dependent'][$i]['firstname']) ? $employee->details['dependent'][$i]['firstname']['value'] : '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="dependentMiddleName[{{$i}}]" class="form-paper-label">Middle name:</label>
                                <input id="dependentMiddleName[{{$i}}]" name="dependent_middle_name[{{$i}}]" type="text" class="form-control" value="{{ key_exists('dependent', $employee->details) && isset($employee->details['dependent'][$i]['middlename']) ? $employee->details['dependent'][$i]['middlename']['value'] : '' }}" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 form-paper">
                    <div class="form-group">
                        <label for="dependentRelationship[{{$i}}]" class="form-paper-label">Relationship:</label>
                        <input id="dependentRelationship[{{$i}}]" name="dependent_relationship[{{$i}}]" type="text" class="form-control" value="{{ key_exists('dependent', $employee->details) && isset($employee->details['dependent'][$i]['relationship']) ? $employee->details['dependent'][$i]['relationship']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-12 form-paper section-divider"></div>

            </div>

            @endfor

            <div class="row addContainer">
                <div class="col-12 form-paper">
                    <button class="btn btn-link" type="button" onclick="createNewRow(this, 'dependent')">Add Dependent</button>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>


            <div class="row">
                <div class="col-sm-6 col-12 form-paper">
                    <div class="form-group">
                        <label for="phoneNumber" class="form-paper-label">Address</label>
                        <input id="address" type="text" name="presentaddress" class="form-control" value="{{  $employee->details != null && key_exists('presentaddress', $employee->details) ? $employee->details['presentaddress']['value']: '' }}" />
                    </div>
                </div>
                <div class="col-sm-3 col-12 form-paper">
                    <div class="form-group">
                        <label for="phoneNumber" class="form-paper-label">Phone Number</label>
                        <input id="phoneNumber" type="text" name="contact_number" class="form-control" value="{{  $employee->details != null && key_exists('contactnumber', $employee->details) ? $employee->details['contactnumber']['value']: '' }}" />
                    </div>
                </div>
                <div class="col-sm-3 col-12 form-paper">
                    <div class="form-group">
                        <label for="email" class="form-paper-label">Email Address</label>
                        <input id="email" type="text" name="email_address" class="form-control" value="{{  $employee->details != null && key_exists('emailaddress', $employee->details) ? $employee->details['emailaddress']['value']: '' }}" />
                    </div>
                </div>
                <div class="col-12 form-paper">
                    <div class="row">
                        <div class="col-12 section-label">Person to contact in case of emergency</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-8 col-12">
                            <div class="form-group">
                                <label for="emergencyName" class="form-paper-label">Name</label>
                                <input id="address" type="text" name="emergency_name" class="form-control" value="{{  $employee->details != null && key_exists('emergencyname', $employee->details) ? $employee->details['emergencyname']['value']: '' }}" />
                            </div>
                        </div>
                        <div class="col-sm-4 col-12">
                            <div class="form-group">
                                <label for="emergencyContact" class="form-paper-label">Contact</label>
                                <input id="emergencyContact" type="text" name="emergency_phone" class="form-control" value="{{  $employee->details != null && key_exists('emergencyphone', $employee->details) ? $employee->details['emergencyphone']['value']: '' }}" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col form-paper section-title">Employment Information</div>
                <div class="col-12 form-paper section-divider"></div>
            </div>

            <div class="row">
                <div class="col-md-2 col-5 form-paper">
                    <div class="form-group">
                        <label for="timeCard" class="form-paper-label">Time Card #</label>
                        <input id="timeCard" type="text" name="time_card" class="form-control" value="{{ $employee->current != null && key_exists('timecard', $employee->current) ? $employee->current['timecard'] : '' }}" required />
                    </div>
                </div>
                <div class="col-md-3 col-7 form-paper">
                    <div class="form-group">
                        <label for="department" class="form-paper-label">Department/Project</label>
                        <select id="department" type="text" onchange="updateTimeInTimeOut(this)" name="department" class="form-control" value="{{ $employee->current != null &&  key_exists('department', $employee->current) ? $employee->current['department']['value'] : '' }}" >
                            <option value="0"></option>
                            @foreach($categories['department'] as $category)
                            <option value="{{ $category->id }}" {{ $employee->current != null &&  key_exists('department', $employee->current) && $employee->current['department']['value'] == $category->id ? 'selected' : '' }} >{{ $category->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-7 form-paper">
                    <div class="form-group">
                        <label for="position" class="form-paper-label">Position</label>
                        <input id="position" type="text" name="position" class="form-control" value="{{  $employee->current != null && key_exists('position', $employee->current) ? $employee->current['position'] : '' }}" />
                    </div>
                </div>
                <div class="col-md-3 col-5 form-paper">
                    <div class="form-group">
                        <label for="employmentType" class="form-paper-label">Employment Type</label>
                        <select id="employmentType" type="text" name="employment_type" class="form-control" value="{{  $employee->current != null && key_exists('employmenttype', $employee->current) ? $employee->current['employmenttype']['value'] : '' }}" >
                            @foreach($categories['employmenttype'] as $category)
                            <option value="{{ $category->id }}" {{ $employee->current != null &&  key_exists('employmenttype', $employee->current) && $employee->current['employmenttype']['value'] == $category->id ? 'selected' : '' }} >{{ $category->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-4 col-6 form-paper">
                    <div class="form-group">
                        <label for="dateHired" class="form-paper-label">Date Started</label>
                        <input id="dateHired" type="date" name="date_started" class="form-control" value="{{  $employee->current != null && key_exists('datestarted', $employee->current) ? $employee->current['datestarted']: '' }}" />
                    </div>
                </div>
                <div class="col-sm-4 col-6 form-paper">
                    <div class="form-group">
                        <label for="dateEnded" class="form-paper-label">Until</label>
                        <input id="dateEnded" type="date" name="date_transfered" class="form-control" value="{{  $employee->current != null && key_exists('datetransfered', $employee->current) ? $employee->current['datetransfered'] : '' }}" />
                    </div>
                </div>
                <div class="col-sm-4 col-12 form-paper">
                    <div class="form-group">
                        <label for="status" class="form-paper-label">Status</label>
                        <select id="status" name="contract_status" class="form-control" value="{{  $employee->current != null && key_exists('contractstatus', $employee->current) ? $employee->current['contractstatus']['value'] : '' }}" >
                            @foreach($categories['contractstatus'] as $category)
                            <option value="{{ $category->id }}" {{  $employee->current != null && key_exists('contractstatus', $employee->current) && $employee->current['contractstatus']['value'] == $category->id ? 'selected' : '' }} >{{ $category->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-6 form-paper">
                    <div class="form-group">
                        <label for="typeOfPayment" class="form-paper-label">Type of payment</label>
                        <select id="typeOfPayment" name="payment_type" class="form-control" value="{{ $employee->current != null &&  key_exists('paymenttype', $employee->current) ? $employee->current['paymenttype']['value'] : '' }}">
                            @foreach($categories['paymenttype'] as $category)
                            <option value="{{ $category->id }}" {{ $employee->current != null &&  key_exists('paymenttype', $employee->current) && $employee->current['paymenttype']['value'] == $category->id ? 'selected' : '' }} >{{ $category->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2 col-6 form-paper">
                    <div class="form-group">
                        <label for="modeOfPayment" class="form-paper-label">Mode of payment</label>
                        <select id="modeOfPayment" name="payment_mode" class="form-control" value="{{  $employee->current != null && key_exists('paymentmode', $employee->current) ? $employee->current['paymentmode']['value'] : '' }}" >
                            @foreach($categories['paymentmode'] as $category)
                            <option value="{{ $category->id }}" {{  $employee->current != null && key_exists('paymentmode', $employee->current) && $employee->current['paymentmode']['value'] == $category->id ? 'selected' : '' }} >{{ $category->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-7 form-paper">
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                <label for="rateBasis" class="form-paper-label">Rate Basis</label>
                                <div class="form-check">
                                    <input id="dailyBasis" type="radio" name="rate_basis" class="form-check-input" value="daily" {{ $employee->current != null && isset($employee->current['ratebasis'])  && $employee->current['ratebasis'] == 'daily' ? 'checked' : '' }} />
                                    <label for="dailyBasis" class="form-paper-label">Daily</label>
                                </div>
                                <div class="form-check">
                                    <input id="monthlyBasis" type="radio" name="rate_basis" class="form-check-input" value="monthly" {{ $employee->id == 0 ? 'checked' : '' }} {{ $employee->current != null && isset($employee->current['ratebasis'])  && $employee->current['ratebasis'] == 'monthly' ? 'checked' : '' }} />
                                    <label for="monthlyBasis" class="form-paper-label">Monthly</label>
                                </div>
                                <div class="form-check">
                                    <input id="fixedBasis" type="radio" name="rate_basis" class="form-check-input" value="fixed" {{ $employee->current != null && isset($employee->current['ratebasis'])  && $employee->current['ratebasis'] == 'fixed' ? 'checked' : '' }} />
                                    <label for="fixedBasis" class="form-paper-label">Fixed</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label for="rate" class="form-paper-label">Rate</label>
                                <input id="rate" type="number" name="rate" step="0.05" class="form-control" value="{{ $employee->current != null &&  key_exists('rate', $employee->current) ? $employee->current['rate'] : '' }}" required />
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="allowance" class="form-paper-label">Allowance</label>
                                <input id="allowance" type="number" name="allowance" step="0.05" class="form-control" value="{{  $employee->current != null && key_exists('allowance', $employee->current) ? $employee->current['allowance'] : '' }}" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 form-paper">
                    <div class="row">
                        <div class="col-12 section-label">Schedule</div>
                        <div class="col-sm-5 col-6">
                            <div class="form-group">
                                <label for="timeIn" class="form-paper-label">Time In</label>
                                <input type="hidden" name="schedule_id" value="{{  $employee->timeTable != null && key_exists('id', $employee->timeTable) ? $employee->timeTable['id'] : '' }}" />
                                <input id="timeIn" type="time" name="time_in" class="form-control"  value="{{  $employee->timeTable != null && key_exists('timein', $employee->timeTable) ? date_format(date_create($employee->timeTable['timein']), 'H:i') : '' }}" required />
                            </div>
                        </div>
                        <div class="col-sm-5 col-6">
                            <div class="form-group">
                                <label for="timeOut" class="form-paper-label">Time Out</label>
                                <input id="timeOut" type="time" name="time_out" class="form-control"  value="{{ $employee->timeTable != null && key_exists('timeout', $employee->timeTable) ? date_format(date_create($employee->timeTable['timeout']), 'H:i') : '' }}" required />
                            </div>
                        </div>
                        <div class="col-sm-2 col-12">
                            <div class="form-group">
                                <label for="break" class="form-paper-label">Break</label>
                                <input id="break" type="number" name="break" class="form-control"  value="{{ $employee->timeTable != null && key_exists('break', $employee->timeTable) ? $employee->timeTable['break'] : '' }}" required />
                            </div>
                        </div>
                        <div class="col-sm-4 col-6">
                            <div class="form-group">
                                <label for="effectiveDateStart" class="form-paper-label">Change Shift Schedule</label>
                                <input id="effectiveDateStart" type="date" name="effective_date_start" class="form-control"  value="{{ $employee->timeTable != null && key_exists('startdate', $employee->timeTable) ? $employee->timeTable['startdate'] : date_format(NOW(), 'Y-m-d') }}" />
                            </div>
                        </div>
                        <div class="col-sm-4 col-6">
                            <div class="form-group">
                                <label for="effectiveDateEnd" class="form-paper-label">Until</label>
                                <input id="effectiveDateEnd" type="date" name="effective_date_end" class="form-control"  value="{{ $employee->timeTable != null && key_exists('enddate', $employee->timeTable) ? $employee->timeTable['enddate'] : '' }}" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <button type="button" class="btn btn-link" data-toggle="modal" data-target="#scheduleHistoryModal">View Schedule History</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 form-paper section-divider"></div>
            </div>

            @if ($employee->id != 0)
            <div class="row">
                <div class="col-12 form-paper">
                    <div class="btn-group">
                        <button type="button" class="btn btn-link" data-toggle="modal" data-target="#transferEmployeeModal">Transfer Employee</button>
                        <button type="button" class="btn btn-link" data-toggle="modal" data-target="#transferHistoryModal">View History of Transfers</button>
                    </div>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>
            @endif

            <div class="row">
                <div class="col-12 form-paper section-title">Other Information</div>
                <div class="col-12 form-paper section-divider"></div>
            </div>
            <div class="row">
                <div class="col-12 form-paper">
                    <label class="form-paper-label">Benefits:</label><br/>
                    <div class="form-group">
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" name="tin" {{ key_exists('tin', $employee->deductibles) && $employee->deductibles['tin']['isset'] ? 'checked' : '' }} />
                                TIN
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" name="sss" {{ key_exists('sss', $employee->deductibles) && $employee->deductibles['sss']['isset'] ? 'checked' : '' }} />
                                SSS
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" name="philhealth" {{ key_exists('philhealth', $employee->deductibles) && $employee->deductibles['philhealth']['isset'] ? 'checked' : '' }} />
                                PhilHealth
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" name="pagibig" {{ key_exists('pagibig', $employee->deductibles) && $employee->deductibles['pagibig']['isset'] ? 'checked' : '' }} />
                                PAGIBIG
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md form-paper">
                    <div class="form-group">
                        <label for="tin" class="form-paper-label">TIN</label>
                        <input id="tin" type="text" class="form-control" name="tinnumber" value="{{ key_exists('tin', $employee->deductibles) ? $employee->deductibles['tin']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-md form-paper">
                    <div class="form-group">
                        <label for="ssn" class="form-paper-label">SS #</label>
                        <input id="ssn" type="text" class="form-control" name="ssnumber" value="{{ key_exists('sss', $employee->deductibles) ? $employee->deductibles['sss']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-md form-paper">
                    <div class="form-group">
                        <label for="philhealth" class="form-paper-label">PhilHealth</label>
                        <input id="philhealth" type="text" class="form-control" name="philhealthnumber" value="{{ key_exists('philhealth', $employee->deductibles) ? $employee->deductibles['philhealth']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-md form-paper">
                    <div class="form-group">
                        <label for="pagibig" class="form-paper-label">PAGIBIG</label>
                        <input id="pagibig" type="text" class="form-control" name="pagibignumber" value="{{ key_exists('pagibig', $employee->deductibles) ? $employee->deductibles['pagibig']['value'] : '' }}" />
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-2 form-paper">
                    <div class="form-group">
                        <label for="memo" class="form-paper-label">Number of Memo</label>
                        <input id="memo" type="number" name="number_of_memo" class="form-control"  value="{{ key_exists('numberofmemo', $employee->details) ? $employee->details['numberofmemo']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-md-10 form-paper">
                    <div class="form-group">
                        <label for="remarks" class="form-paper-label">Remarks</label>
                        <input id="remarks" type="text" name="remarks" class="form-control" value="{{ key_exists('remarks', $employee->details ) ? $employee->details['remarks']['value'] : '' }}" />
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-6 form-paper">
                    <div class="row">
                        @if (key_exists('file', $employee->details))
                        <div  class="col-6">
                            <div class="form-group">
                                <label class="form-paper-label">File</label>
                                <a class="btn btn-link" target="_blank" href="{{ action('EmployeeController@downloadFile', key_exists('file', $employee->details) ? $employee->details['file']['value'] : '') }}">{{ key_exists('file', $employee->details) ? $employee->details['file']['value'] : '' }}</a>
                                <input type="hidden" name="file_old" value="{{ key_exists('file', $employee->details) ? $employee->details['file']['value'] : '' }}" />
                            </div>
                        </div>
                        @endif
                        <div  class="col">
                            <div class="form-group">
                                <label for="file" class="form-paper-label">New File</label>
                                <input id="file" type="file" name="file_new" class="form-control-file " />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 form-paper">
                    <div class="form-group">
                        <label for="fileDetails" class="form-paper-label">File Details</label>
                        <input id="fileDetails" type="text" name="file_details" class="form-control" value="{{ key_exists('file', $employee->details ) ? $employee->details['file']['displayName'] : '' }}" />
                    </div>
                </div>
            </div>

            <div class="m-4">&nbsp;</div>
            <div class="fixed-bottom btn-container m-4">
                <div class="float-right">
                    <div class="btn-group">
                        <a class="btn btn-light" href="{{ action('EmployeeController@index') }}">Back to List</a>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <input type="submit" class="btn btn-primary" data-confirm="save" value="Save"/>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

{{-- Image Select Modal --}}
<div class="modal fade" role="dialog" id="selectPictureModal" tabindex="-1" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <span class="lead">Select image</span>
                <button type="button" class="close" data-dismiss='modal'>&times;</button>
            </div>
            <div class="modal-body">

                {{-- If update, present a different form for file input --}}
                @if ($employee->id != 0)
                <form id="imageForm" action="{{ action('EmployeeController@updateImage', $employee->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="selectedFilename" name="selected_filename" />
                    <input type="hidden" id="selectedLocation" name="selected_location" />
                    <div class="d-inline-block">
                        <input type="file" class="form-control-file" accept="image/*" name="picture_file" />
                    </div>
                    <div class="d-inline-sm-block float-sm-right">
                        <input type="reset" class="btn btn-light btn-sm" value="Reset" />
                    </div>
                </form>
                @endif

                <hr />
                @if (is_array($employee->pictures) && sizeof($employee->pictures) != 0)
                <div class="lead">Previous Images</div>
                    <?php $index = 0 ?>
                    @foreach($employee->pictures as $pic)

                        @if ($index % 4 == 0)
                        <div class="row mb-4">
                        @endif

                        <div class="col-sm-3 col-6 previous-image" data-location="{{ $pic['location'] }}" data-filename={{  $pic['filename'] }}>
                            <form action="{{ action('EmployeeController@deleteImage', $employee->id) }}" method="POST" >
                                @csrf
                                <input type="hidden" name="location" value="{{ $pic['location'] }}" />
                                <input type="hidden" name="filename" value="{{ $pic['filename'] }}" />
                                <div class="dropdown">
                                        <div class="dropdown-menu  dropdown-menu-right">
                                            <button type="submit" class="dropdown-item" >Delete</button>
                                            <button type="button" class="dropdown-item" >Cancel</button>
                                        </div>
                                    </div>
                                <button type="button" class="btn btn-light btn-delete-image" data-toggle="dropdown"><i class="fa fa-trash fa-lg"></i></button>

                            </form>
                            <img src="{{ asset('storage/'.$pic['location'].$pic['filename']) }}" class="img-fluid" onclick="selectImage(this)" />
                        </div>

                        @if (($index+1) % 4 == 0)
                        </div>
                        @endif
                        <?php
                        $index++;
                        if ($index >= 8) break;
                        ?>
                    @endforeach
                @else
                    <div class="text-center text-muted"><i>No previous pictures</i></div>
                @endif
            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-primary" value="Save" form="imageForm" data-confirm="save" />
            </div>
        </div>
    </div>
</div>
</div>


{{-- Transfer employee --}}
<div class="modal fade" role="dialog" id="transferEmployeeModal" >
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ action('EmployeeController@transferEmployee', $employee->id) }}" method="post">
                @csrf
                @method('post')
                <div class="row">
                    <div class="col-12 form-paper section-title">Transfer Employee
                        <button type="button" class="close" data-dismiss='modal'>&times;</button>
                    </div>
                    <div class="col-12 form-paper section-divider"></div>
                </div>
                <div class="row">
                    <div class="col-md-2 col-5 form-paper">
                        <div class="form-group">
                            <label for="newTimecard" class="form-paper-label">Time Card</label>
                            <input id="newTimecard" type="text" class="form-control" name="time_card" />
                        </div>
                    </div>
                    <div class="col-md-3 col-7 form-paper">
                        <div class="form-group">
                            <label for="newDepartment" class="form-paper-label">Department</label>
                            <select id="newDepartment" name="department" class="form-control" onchange="updateTimeInTimeOutOnModal(this)">
                                <option></option>
                                @foreach($categories['department'] as $category)
                                <option value="{{ $category->id }}" >{{ $category->value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-7 form-paper">
                        <div class="form-group">
                            <label for="newPosition" class="form-paper-label">Position</label>
                            <input type="text" id="newPosition" name="position" class="form-control" required />
                        </div>
                    </div>
                    <div class="col-md-3 col-5 form-paper">
                        <div class="form-group">
                            <label for="employmentType" class="form-paper-label">Employment Type</label>
                            <select id="employmentType" type="text" name="employment_type" class="form-control">
                                <option></option>
                                @foreach($categories['employmenttype'] as $category)
                                <option value="{{ $category->id }}">{{ $category->value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4 form-paper">
                        <div class="form-group">
                            <label for="mewDateHired" class="form-paper-label">Employment Start Date</label>
                            <input id="mewDateHired" type="date" name="date_started" class="form-control" required />
                        </div>
                    </div>
                    <div class="col-4 form-paper">
                        <div class="form-group">
                            <label for="newDateEnded" class="form-paper-label">Unemployment Date</label>
                            <input id="newDateEnded" type="date" name="date_transfered" class="form-control" />
                        </div>
                    </div>
                    <div class="col-4 form-paper">
                        <div class="form-group">
                            <label for="newStatus" class="form-paper-label">Status</label>
                            <select id="newStatus" name="contract_status" class="form-control">
                                @foreach($categories['contractstatus'] as $category)
                                <option value="{{ $category->id }}" >{{ $category->value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 form-paper">
                        <div class="form-group">
                            <label for="newTypeOfPayment" class="form-paper-label">Type of payment</label>
                            <select id="newTypeOfPayment" name="payment_type" class="form-control">
                                @foreach($categories['paymenttype'] as $category)
                                <option value="{{ $category->id }}" >{{ $category->value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 form-paper">
                        <div class="form-group">
                            <label for="newModeOfPayment" class="form-paper-label">Mode of payment</label>
                            <select id="newModeOfPayment" name="payment_mode" class="form-control">
                                @foreach($categories['paymentmode'] as $category)
                                <option value="{{ $category->id }}" >{{ $category->value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-7 form-paper">
                        <div class="row">
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="rateBasis" class="form-paper-label">Rate Basis</label>
                                    <div class="form-check">
                                        <input id="newDailyBasis" type="radio" name="rate_basis" class="form-check-input" value="daily" />
                                        <label for="newDailyBasis" class="form-paper-label">Daily</label>
                                    </div>
                                    <div class="form-check">
                                        <input id="newMonthlyBasis" type="radio" name="rate_basis" class="form-check-input" value="monthly" checked />
                                        <label for="newMonthlyBasis" class="form-paper-label">Monthly</label>
                                    </div>
                                    <div class="form-check">
                                        <input id="newFixedBasis" type="radio" name="rate_basis" class="form-check-input" value="fixed" checked />
                                        <label for="newFixedBasis" class="form-paper-label">Fixed</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="form-group">
                                    <label for="newRate" class="form-paper-label">Rate</label>
                                    <input id="newRate" type="number" name="rate" step="0.05" class="form-control" value="{{ $employee->current != null &&  key_exists('rate', $employee->current) ? $employee->current['rate'] : '' }}" required />
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="newAllowance" class="form-paper-label">Allowance</label>
                                    <input id="newAllowance" type="number" name="allowance" step="0.05" class="form-control" value="{{  $employee->current != null && key_exists('allowance', $employee->current) ? $employee->current['allowance'] : '' }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-5 form-paper">
                        <div class="form-group">
                            <label for="timeInModal" class="form-paper-label">Time In</label>
                            <input id="timeInModal" type="time" name="time_in" class="form-control" required />
                        </div>
                    </div>
                    <div class="col-md-5 form-paper">
                        <div class="form-group">
                            <label for="timeOutModal" class="form-paper-label">Time Out</label>
                            <input id="timeOutModal" type="time" name="time_out" class="form-control"  required />
                        </div>
                    </div>
                    <div class="col-md-2 form-paper">
                        <div class="form-group">
                            <label for="breakModal" class="form-paper-label">Break (hrs)</label>
                            <input id="breakModal" type="number" name="break" class="form-control" required />
                        </div>
                    </div>
                    <div class="col-12 form-paper section-divider"></div>
                </div>

                <div class="row">
                    <div class="col-12 form-paper">
                        <div class="form-group">
                            <div class="float-right">
                                <input type="submit" class="btn btn-primary" value="Transfer" data-confirm="save" />
                            </div>
                        </div>
                        <div class="mb-2">&nbsp;</div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- Transfer History --}}
<div class="modal fade" role="dialog" id="transferHistoryModal" >
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="row">
                <div class="col-12 form-paper section-title"><span id="title">{{ $employee->fullName }} - Transfer History</span>
                    <button type="button" class="close" data-dismiss='modal'>&times;</button>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>
            <div class="row">
                <div class="col-12 form-paper">
                    <div style="overflow-x: auto;">
                        <table class="table table-sm" style="font-size:0.75em;" id="transferHistoryTable">
                            <thead>
                                <tr>
                                    <th rowspan="2">ID</th>
                                    <th rowspan="2">Last Name</th>
                                    <th rowspan="2">First Name</th>
                                    <th rowspan="2">Middle Name</th>
                                    <th colspan="3">Current</th>
                                    <th colspan="3">New</th>
                                    <th rowspan="2">Date Started</th>
                                    <th rowspan="2">Date Transfered</th>
                                </tr>
                                <tr>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>TC #</th>
                                    <th>Department </th>
                                    <th>Position </th>
                                    <th>TC # </th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < sizeof($employee->history); $i++)
                                <tr>
                                    <td>{{ $employee->employeeId }}</td>
                                    <td>{{ $employee->lastName }}</td>
                                    <td>{{ $employee->firstName }}</td>
                                    <td>{{ $employee->middleName }}</td>
                                    <td>{{ $employee->history[$i]['department']['displayName'] }}</td>
                                    <td>{{ $employee->history[$i]['position'] }}</td>
                                    <td>{{ $employee->history[$i]['timecard'] }}</td>
                                    @if ($i+1 != sizeof($employee->history))
                                    <td>{{ $employee->history[$i+1]['department']['displayName'] }}</td>
                                    <td>{{ $employee->history[$i+1]['position'] }}</td>
                                    <td>{{ $employee->history[$i+1]['timecard'] }}</td>
                                    @else
                                    <td><i class="text-muted"></i></td>
                                    <td></td>
                                    <td></td>
                                    @endif
                                    <td>{{ $employee->history[$i]['datestarted'] != null ? date_format(date_create($employee->history[$i]['datestarted']), 'M d, Y') : '' }}</td>
                                    <td>{{ $employee->history[$i]['datetransfered'] != null ? date_format(date_create($employee->history[$i]['datetransfered']), 'M d, Y') : '' }}</td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 form-paper">
                    <div class="form-group">
                        <div class="float-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-secondary" onclick="saveAsPDF()">Save as PDF</button>
                                <button type="button" class="btn btn-secondary" onclick="saveAsExcel()">Save as Excel</button>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">&nbsp;</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Schedule History --}}
<div class="modal fade" role="dialog" id="scheduleHistoryModal" >
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="row">
                <div class="col-12 form-paper section-title"><span id="title">{{ $employee->fullName }} - History of Schedules</span>
                    <button type="button" class="close" data-dismiss='modal'>&times;</button>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>
            <div class="row">


                <div class="col-12 form-paper">
                    <table class="table table-sm" id="transferHistoryTable">
                        <thead>
                            <tr>
                                <th>Time In</th>
                                <th>Time out</th>
                                <th>Break</th>
                                <th>Date Effective</th>
                                <th>Until </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($employee->timeTableHistory != null && sizeof($employee->timeTableHistory) > 0)
                            @for ($i = 0; $i < sizeof($employee->timeTableHistory); $i++)
                            <tr {!! $employee->timeTable != null && isset($employee->timeTable['id']) && $employee->timeTableHistory[$i]['id'] == $employee->timeTable['id'] ? 'class=highlighted' : '' !!}>
                                <td>{{ isset($employee->timeTableHistory[$i]['timein']) ? date_format(date_create($employee->timeTableHistory[$i]['timein']), 'H:i') : '' }}</td>
                                <td>{{ isset($employee->timeTableHistory[$i]['timeout']) ? date_format(date_create($employee->timeTableHistory[$i]['timeout']), 'H:i') : '' }}</td>
                                <td>{{ $employee->timeTableHistory[$i]['break'] }}</td>
                                <td>{{ isset($employee->timeTableHistory[$i]['startdate']) ? date_format(date_create($employee->timeTableHistory[$i]['startdate']), 'M d, Y') : ''  }}</td>
                                <td>{{ isset($employee->timeTableHistory[$i]['enddate']) ? date_format(date_create($employee->timeTableHistory[$i]['enddate']), 'M d, Y') : '' }}</td>
                            </tr>
                            @endfor
                            @else
                            <tr>
                                <td colspan="5"><em class="text-muted">No schedule history</em></td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('script')
<script src="{{ asset('js/imageSelection.js') }}"></script>
<script src="{{ asset('js/dynamicAddAndDelete.js') }}"></script>
<script src="{{ asset('js/contactDetailManager.js') }}"></script>
<script src="{{ asset('js/employeePage.js') }}"></script>
@stop
