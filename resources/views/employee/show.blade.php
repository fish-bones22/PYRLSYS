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
                <img src="{{ $employee->currentPicture == null ? asset('img/anom.png') : asset('storage/'.$employee->currentPicture['location'].$employee->currentPicture['filename']) }}" class="img-fluid" />
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
                <div class="col-md-6 form-paper">
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
                                <input id="spouseLastName" name="spouse_last_name[0]" type="text" class="form-control" value="{{ key_exists('spouse', $employee->details) ? $employee->details['spouse'][0]['lastname']['value'] : '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="spouseFirstName" class="form-paper-label">First name:</label>
                                <input id="spouseFirstName" name="spouse_first_name[0]" type="text" class="form-control" value="{{ key_exists('spouse', $employee->details) ? $employee->details['spouse'][0]['firstname']['value'] : '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="spouseMiddleName" class="form-paper-label">Middle name:</label>
                                <input id="spouseMiddleName" name="spouse_middle_name[0]" type="text" class="form-control" value="{{ key_exists('spouse', $employee->details) ? $employee->details['spouse'][0]['middlename']['value'] : '' }}" />
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
                                <input id="dependentLastName[{{$i}}]" name="dependent_last_name[{{$i}}]" type="text" class="form-control" value="{{ key_exists('dependent', $employee->details) ? $employee->details['dependent'][$i]['lastname']['value'] : '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="dependentFirstName[{{$i}}]" class="form-paper-label">First name:</label>
                                <input id="dependentFirstName[{{$i}}]" name="dependent_first_name[{{$i}}]" type="text" class="form-control" value="{{ key_exists('dependent', $employee->details) ? $employee->details['dependent'][$i]['firstname']['value'] : '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="dependentMiddleName[{{$i}}]" class="form-paper-label">Middle name:</label>
                                <input id="dependentMiddleName[{{$i}}]" name="dependent_middle_name[{{$i}}]" type="text" class="form-control" value="{{ key_exists('dependent', $employee->details) ? $employee->details['dependent'][$i]['middlename']['value'] : '' }}" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 form-paper">
                    <div class="form-group">
                        <label for="dependentRelationship[{{$i}}]" class="form-paper-label">Relationship:</label>
                        <input id="dependentRelationship[{{$i}}]" name="dependent_relationship[{{$i}}]" type="text" class="form-control" value="{{ key_exists('dependent', $employee->details) ? $employee->details['dependent'][$i]['relationship']['value'] : '' }}" />
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
                <div class="col form-paper section-title">Employment Information</div>
                <div class="col-12 form-paper section-divider"></div>
            </div>

            <div class="row">
                <div class="col-md-2 col-5 form-paper">
                    <div class="form-group">
                        <label for="timeCard" class="form-paper-label">Time Card #</label>
                        <input id="timeCard" type="text" name="time_card" class="form-control" value="{{ key_exists('timecard', $employee->details) ? $employee->details['timecard']['value'] : '' }}" required />
                    </div>
                </div>
                <div class="col-md-3 col-7 form-paper">
                    <div class="form-group">
                        <label for="department" class="form-paper-label">Department/Project</label>
                        <select id="department" type="text" onchange="updateTimeInTimeOut(this)" name="department" class="form-control" value="{{ key_exists('department', $employee->employmentDetails) ? $employee->employmentDetails['department']['value'] : '' }}" />
                            <option value="0"></option>
                            @foreach($categories['department'] as $category)
                            <option value="{{ $category->id }}" {{ key_exists('department', $employee->employmentDetails) && $employee->employmentDetails['department']['value'] == $category->id ? 'selected' : '' }} >{{ $category->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-7 form-paper">
                    <div class="form-group">
                        <label for="position" class="form-paper-label">Position</label>
                        <input id="position" type="text" name="position" class="form-control" value="{{ key_exists('position', $employee->details) ? $employee->details['position']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-md-3 col-5 form-paper">
                    <div class="form-group">
                        <label for="employmentType" class="form-paper-label">Employment Type</label>
                        <select id="employmentType" type="text" name="employment_type" class="form-control" value="{{ key_exists('employmenttype', $employee->employmentDetails) ? $employee->employmentDetails['employmenttype']['value'] : '' }}" />
                            @foreach($categories['employmenttype'] as $category)
                            <option value="{{ $category->id }}" {{ key_exists('employmenttype', $employee->employmentDetails) && $employee->employmentDetails['employmenttype']['value'] == $category->id ? 'selected' : '' }} >{{ $category->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label for="dateHired" class="form-paper-label">Date Started</label>
                        <input id="dateHired" type="date" name="date_hired" class="form-control" value="{{ key_exists('datehired', $employee->details) ? $employee->details['datehired']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label for="dateEnded" class="form-paper-label">Until</label>
                        <input id="dateEnded" type="date" name="date_end" class="form-control" value="{{ key_exists('dateend', $employee->details) ? $employee->details['dateend']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label for="status" class="form-paper-label">Status</label>
                        <select id="status" name="contract_status" class="form-control" value="{{ key_exists('contractstatus', $employee->employmentDetails) ? $employee->employmentDetails['contractstatus']['value'] : '' }}" />
                            @foreach($categories['contractstatus'] as $category)
                            <option value="{{ $category->id }}" {{ key_exists('contractstatus', $employee->employmentDetails) && $employee->employmentDetails['contractstatus']['value'] == $category->id ? 'selected' : '' }} >{{ $category->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 form-paper">
                    <div class="form-group">
                        <label for="typeOfPayment" class="form-paper-label">Type of payment</label>
                        <select id="typeOfPayment" name="payment_type" class="form-control" value="{{ key_exists('paymenttype', $employee->employmentDetails) ? $employee->employmentDetails['paymenttype']['value'] : '' }}" />
                            @foreach($categories['paymenttype'] as $category)
                            <option value="{{ $category->id }}" {{ key_exists('paymenttype', $employee->employmentDetails) && $employee->employmentDetails['paymenttype']['value'] == $category->id ? 'selected' : '' }} >{{ $category->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 form-paper">
                    <div class="form-group">
                        <label for="modeOfPayment" class="form-paper-label">Mode of payment</label>
                        <select id="modeOfPayment" name="payment_mode" class="form-control" value="{{ key_exists('paymentmode', $employee->employmentDetails) ? $employee->employmentDetails['paymentmode']['value'] : '' }}" />
                            @foreach($categories['paymentmode'] as $category)
                            <option value="{{ $category->id }}" {{ key_exists('paymentmode', $employee->employmentDetails) && $employee->employmentDetails['paymentmode']['value'] == $category->id ? 'selected' : '' }} >{{ $category->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 form-paper">
                    <div class="form-group">
                        <label for="ratePerHour" class="form-paper-label">Hourly rate</label>
                        <input id="ratePerHour" type="number" name="rate" step="0.05" class="form-control" value="{{ key_exists('rate', $employee->details) ? $employee->details['rate']['value'] : '' }}" required />
                    </div>
                </div>
                <div class="col-sm-3 form-paper">
                    <div class="form-group">
                        <label for="allowance" class="form-paper-label">Allowance</label>
                        <input id="allowance" type="number" name="allowance" step="0.05" class="form-control" value="{{ key_exists('allowance', $employee->details) ? $employee->details['allowance']['value'] : '' }}" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6 form-paper">
                    <div class="form-group">
                        <label for="timeIn" class="form-paper-label">Time In</label>
                        <input id="timeIn" type="time" name="time_in" class="form-control"  value="{{ key_exists('timein', $employee->details) ? $employee->details['timein']['value'] : '' }}" required />
                    </div>
                </div>
                <div class="col-md-6 form-paper">
                    <div class="form-group">
                        <label for="timeOut" class="form-paper-label">Time Out</label>
                        <input id="timeOut" type="time" name="time_out" class="form-control"  value="{{ key_exists('timeout', $employee->details) ? $employee->details['timeout']['value'] : '' }}" required />
                    </div>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>

            <div class="row">
                <div class="col-12 form-paper">
                    <div class="btn-group">
                        <button type="button" class="btn btn-link">Transfer Employee</button>
                        <button type="button" class="btn btn-link">View History of Transfers</button>
                    </div>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>

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
                                <input class="form-check-input" type="checkbox" name="tin" {{ key_exists('tin', $employee->deductibles) ? 'checked' : '' }} />
                                TIN
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" name="sss" {{ key_exists('sss', $employee->deductibles) ? 'checked' : '' }} />
                                SSS
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" name="philhealth" {{ key_exists('philhealth', $employee->deductibles) ? 'checked' : '' }} />
                                PhilHealth
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" name="pagibig" {{ key_exists('pagibig', $employee->deductibles) ? 'checked' : '' }} />
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
                        <input id="tin" type="text" class="form-control" name="tinnumber" value="{{ key_exists('tin', $employee->deductibles) ? $employee->deductibles['tin'] : '' }}" />
                    </div>
                </div>
                <div class="col-md form-paper">
                    <div class="form-group">
                        <label for="ssn" class="form-paper-label">SS #</label>
                        <input id="ssn" type="text" class="form-control" name="ssnumber" value="{{ key_exists('sss', $employee->deductibles) ? $employee->deductibles['sss'] : '' }}" />
                    </div>
                </div>
                <div class="col-md form-paper">
                    <div class="form-group">
                        <label for="philhealth" class="form-paper-label">PhilHealth</label>
                        <input id="philhealth" type="text" class="form-control" name="philhealthnumber" value="{{ key_exists('philhealth', $employee->deductibles) ? $employee->deductibles['philhealth'] : '' }}" />
                    </div>
                </div>
                <div class="col-md form-paper">
                    <div class="form-group">
                        <label for="pagibig" class="form-paper-label">PAGIBIG</label>
                        <input id="pagibig" type="text" class="form-control" name="pagibignumber" value="{{ key_exists('pagibig', $employee->deductibles) ? $employee->deductibles['pagibig'] : '' }}" />
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

            <div class="m-4">&nbsp;</div>
            <div class="fixed-bottom btn-container m-4">
                <div class="float-right">
                    <div class="btn-group">
                        <a class="btn btn-light" href="{{ action('EmployeeController@index') }}">Back to List</a>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <input type="submit" class="btn btn-primary" value="Save"/>
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
                <input type="submit" class="btn btn-primary" value="Save" form="imageForm" />
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
