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
                        <div class="form-paper-display">{{ $employee->employeeId }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 form-paper">
                    <div class="form-group">
                        <label for="sex" class="form-paper-label">Sex:</label>
                        <div class="form-paper-display">{{$employee->sex == 'm' ? 'Male' : 'Female' }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 form-paper">
                    <div class="form-group">
                        <label for="civilStatus" class="form-paper-label">Civil Status:</label>
                        <div class="form-paper-display">{{ key_exists('civilstatus', $employee->details) ? $employee->details['civilstatus']['value'] : '' }}</div>
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
                                <div class="form-paper-display">{{ $employee->lastName }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group display-block-inline">
                                <label for="firstname" class="form-paper-label">First name:</label>
                                <div class="form-paper-display">{{ $employee->firstName }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group display-block-inline">
                                <label for="middlename" class="form-paper-label">Middle name:</label>
                                <div class="form-paper-display">{{ $employee->middleName }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>

            @if (key_exists('spouse', $employee->details))
            <div class="row">

                <div class="col-md-12 form-paper">
                    <div class="row">
                        <div class="col-12 section-label">Spouse (Maiden) Name</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="spouseLastName" class="form-paper-label">Last name:</label>
                                <div class="form-paper-display">{{ key_exists('spouse', $employee->details) && isset($employee->details['spouse'][0]['lastname']) ? $employee->details['spouse'][0]['lastname']['value'] : '' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="spouseFirstName" class="form-paper-label">First name:</label>
                                <div class="form-paper-display">{{ key_exists('spouse', $employee->details) && isset($employee->details['spouse'][0]['firstname']) ? $employee->details['spouse'][0]['firstname']['value'] : '' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="spouseMiddleName" class="form-paper-label">Middle name:</label>
                                <div class="form-paper-display">{{ key_exists('spouse', $employee->details) && isset($employee->details['spouse'][0]['middlename']) ? $employee->details['spouse'][0]['middlename']['value'] : '' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>
            @endif

            <?php

            // To ensure empty Dependent fields appear,
            // iterate at least one row even if $details['dependent'] is empty

            $limit = 1; // Store the number of dependents or use 1 if there are none.
            // If there is/are dependent/s on the array, use its size instead of the default 1.
            if (key_exists('dependent', $employee->details) && is_array($employee->details['dependent']) && sizeof($employee->details['dependent']) > 0) {
                $limit = sizeof($employee->details['dependent']);
            }

            ?>

            @if (key_exists('dependent', $employee->details))
            <div class="row">
                <div class="col-12 form-paper section-title">Dependent</div>
                <div class="col-12 form-paper section-divider"></div>
            </div>
            @for ($i = 0; $i < $limit; $i++)
            <div class="row dependent-{{$i}}">
                <div class="col-md-8 form-paper">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="dependentLastName[{{$i}}]" class="form-paper-label">Last name:</label>
                                <div class="form-paper-display">{{ key_exists('dependent', $employee->details) && isset($employee->details['dependent'][0]['lastname']) ? $employee->details['dependent'][$i]['lastname']['value'] : '' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="dependentFirstName[{{$i}}]" class="form-paper-label">First name:</label>
                                <div class="form-paper-display">{{ key_exists('dependent', $employee->details) && isset($employee->details['dependent'][0]['firstname']) ? $employee->details['dependent'][$i]['firstname']['value'] : '' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="dependentMiddleName[{{$i}}]" class="form-paper-label">Middle name:</label>
                                <div class="form-paper-display">{{ key_exists('dependent', $employee->details) && isset($employee->details['dependent'][0]['middlename']) ? $employee->details['dependent'][$i]['middlename']['value'] : '' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 form-paper">
                    <div class="form-group">
                        <label for="dependentRelationship[{{$i}}]" class="form-paper-label">Relationship:</label>
                        <div class="form-paper-display">{{ key_exists('dependent', $employee->details) && isset($employee->details['dependent'][0]['relationship']) ? $employee->details['dependent'][$i]['relationship']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-12 form-paper section-divider"></div>

            </div>

            @endfor
            @endif

            <div class="row">
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="phoneNumber" class="form-paper-label">Address</label>
                        <div class="form-paper-display">{{  $employee->details != null && key_exists('address', $employee->details) ? $employee->details['address']['value']: 'No Address' }}</div>
                    </div>
                </div>
                <div class="col-3 form-paper">
                    <div class="form-group">
                        <label for="phoneNumber" class="form-paper-label">Phone Number</label>
                        <div class="form-paper-display">{{  $employee->details != null && key_exists('phonenumber', $employee->details) ? $employee->details['phonenumber']['value']: 'No Phone Number' }}</div>
                    </div>
                </div>
                <div class="col-3 form-paper">
                    <div class="form-group">
                        <label for="email" class="form-paper-label">Email Address</label>
                        <div class="form-paper-display">{{  $employee->details != null && key_exists('email', $employee->details) ? $employee->details['email']['value']: 'No Email' }}</div>
                    </div>
                </div>
                <div class="col-12 form-paper">
                    <div class="row">
                        <div class="col-12 section-label">Person to contact in case of emergency</div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="form-group">
                                <label for="emergencyName" class="form-paper-label">Name</label>
                                <div class="form-paper-display">{{  $employee->details != null && key_exists('emergencyname', $employee->details) ? $employee->details['emergencyname']['value']: 'No Emergency Contact' }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="emergencyContact" class="form-paper-label">Contact</label>
                                <div class="form-paper-display">{{  $employee->details != null && key_exists('emergencyphone', $employee->details) ? $employee->details['emergencyphone']['value']: 'No Emergency Contact #' }}</div>
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
                        <div class="form-paper-display">{{ $employee->current != null && key_exists('timecard', $employee->current) ? $employee->current['timecard'] : '' }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-7 form-paper">
                    <div class="form-group">
                        <label for="department" class="form-paper-label">Department/Project</label>
                        <div class="form-paper-display">{{ $employee->current != null && key_exists('department', $employee->current) ? $employee->current['department']['displayName'] : '' }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-7 form-paper">
                    <div class="form-group">
                        <label for="position" class="form-paper-label">Position</label>
                        <div class="form-paper-display">{{ $employee->current != null && key_exists('position', $employee->current) ? $employee->current['position'] : '' }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-5 form-paper">
                    <div class="form-group">
                        <label for="employmentType" class="form-paper-label">Employment Type</label>
                        <div class="form-paper-display">{{ $employee->current != null && key_exists('employmenttype', $employee->current) ? $employee->current['employmenttype']['displayName'] : '' }}</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label for="dateHired" class="form-paper-label">Date Started</label>
                        <div class="form-paper-display">{{ $employee->current != null && key_exists('datestarted', $employee->current) ? date_format(date_create($employee->current['datestarted']), 'M d, Y'): '' }}</div>
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label for="dateEnded" class="form-paper-label">Until</label>
                        <div class="form-paper-display">{{ $employee->current != null && isset($employee->current['datetransfered']) ? date_format(date_create($employee->current['datetransfered']), 'M d, Y'): 'None' }}</div>
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label for="status" class="form-paper-label">Status</label>
                        <div class="form-paper-display">{{ $employee->current != null && isset($employee->current['contractstatus']['displayName']) ? $employee->current['contractstatus']['displayName'] : 'None' }}</div>
                    </div>
                </div>
                <div class="col-md-3 form-paper">
                    <div class="form-group">
                        <label for="typeOfPayment" class="form-paper-label">Type of payment</label>
                        <div class="form-paper-display">{{ $employee->current != null && isset($employee->current['paymenttype']['displayName']) ? $employee->current['paymenttype']['displayName'] : 'None' }}</div>
                    </div>
                </div>
                <div class="col-md-3 form-paper">
                    <div class="form-group">
                        <label for="modeOfPayment" class="form-paper-label">Mode of payment</label>
                        <div class="form-paper-display">{{ $employee->current != null && isset($employee->current['paymentmode']['displayName']) ? $employee->current['paymentmode']['displayName'] : 'None' }}</div>
                    </div>
                </div>
                <div class="col-md-6 form-paper">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="rateBasis" class="form-paper-label">Rate Basis</label>
                                <div class="form-paper-display">{{ isset($employee->current['ratebasis']) ? $employee->current['ratebasis'] : 'Not set' }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="ratePerHour" class="form-paper-label">Rate</label>
                                <div class="form-paper-display">{{ $employee->current != null && isset($employee->current['rate']) ? $employee->current['rate'] : 'None' }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="allowance" class="form-paper-label">Allowance</label>
                                <div class="form-paper-display">{{ $employee->current != null && isset($employee->current['allowance']) ? $employee->current['allowance'] : 'None' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-5 form-paper">
                    <div class="form-group">
                        <label for="timeIn" class="form-paper-label">Time In</label>
                        <div class="form-paper-display">{{ $employee->current != null && isset($employee->current['timein']) ? date_format(date_create($employee->current['timein']), 'h:i A') : 'None' }}</div>
                    </div>
                </div>
                <div class="col-md-5 form-paper">
                    <div class="form-group">
                        <label for="timeOut" class="form-paper-label">Time Out</label>
                        <div class="form-paper-display">{{ $employee->current != null && isset($employee->current['timeout']) ? date_format(date_create($employee->current['timeout']), 'h:i A') : 'None' }}</div>
                    </div>
                </div>
                <div class="col-md-2 form-paper">
                    <div class="form-group">
                        <label for="break" class="form-paper-label">Break</label>
                        <div class="form-paper-display">{{ $employee->current != null && isset($employee->current['break']) ? $employee->current['break'] : 'None' }}</div>
                    </div>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>

            <div class="row">
                <div class="col-12 form-paper">
                    <div class="btn-group">
                        <button type="button" class="btn btn-link" data-toggle="modal" data-target="#transferHistoryModal">View History of Transfers</button>
                    </div>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>

            <div class="row">
                <div class="col-12 form-paper section-title">Other Information</div>
                <div class="col-12 form-paper section-divider"></div>
            </div>
            <div class="row">
                <div class="col-md form-paper">
                    <div class="form-group">
                        <label for="tin" class="form-paper-label">TIN</label>
                        <div class="form-paper-display">{{ $employee->deductibles != null && isset($employee->deductibles['tin']) ?  $employee->deductibles['tin'] : 'None' }}</div>
                    </div>
                </div>
                <div class="col-md form-paper">
                    <div class="form-group">
                        <label for="ssn" class="form-paper-label">SS #</label>
                        <div class="form-paper-display">{{ $employee->deductibles != null && isset($employee->deductibles['sss']) ?  $employee->deductibles['sss'] : 'None' }}</div>
                    </div>
                </div>
                <div class="col-md form-paper">
                    <div class="form-group">
                        <label for="philhealth" class="form-paper-label">PhilHealth</label>
                        <div class="form-paper-display">{{ $employee->deductibles != null && isset($employee->deductibles['philhealth']) ?  $employee->deductibles['philhealth'] : 'None' }}</div>
                    </div>
                </div>
                <div class="col-md form-paper">
                    <div class="form-group">
                        <label for="pagibig" class="form-paper-label">PAGIBIG</label>
                        <div class="form-paper-display">{{ $employee->deductibles != null && isset($employee->deductibles['pagibig']) ?  $employee->deductibles['pagibig'] : 'None' }}</div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-2 form-paper">
                    <div class="form-group">
                        <label for="memo" class="form-paper-label">Number of Memo</label>
                        <div class="form-paper-display">{{ $employee->details != null && isset($employee->details['numberofmemo']['value']) ?  $employee->details['numberofmemo']['value'] : 'None' }}</div>
                    </div>
                </div>
                <div class="col-md-10 form-paper">
                    <div class="form-group">
                        <label for="remarks" class="form-paper-label">Remarks</label>
                        <div class="form-paper-display">{{ $employee->details != null && isset($employee->details['remarks']['value']) ?  $employee->details['remarks']['value'] : 'None' }}</div>
                    </div>
                </div>
            </div>

            <div class="m-4">&nbsp;</div>
            <div class="fixed-bottom btn-container m-4">
                <div class="float-right">
                    <div class="btn-group">
                        <a class="btn btn-light" href="{{ action('EmployeeController@index') }}">Back to List</a>
                        <button class="btn btn-secondary" onclick="">Save as PDF</button>
                        <a class="btn btn-primary" href="{{ action('EmployeeController@show', $employee->id) }}">Edit</a>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

{{-- Transfer History --}}
<div class="modal fade" role="dialog" id="transferHistoryModal" >
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="row">
                <div class="col-12 form-paper section-title"><span id="title">Transfer History - {{ $employee->fullName }}</span>
                    <button type="button" class="close" data-dismiss='modal'>&times;</button>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>
            <div class="row">
                <div class="col-12 form-paper">
                    <table class="table table-sm" style="font-size:0.75em;" id="transferHistoryTable">
                        <thead>
                            <tr>
                                <th rowspan="2">ID</th>
                                <th rowspan="2">Name</th>
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
                                <td>{{ $employee->fullName }}</td>
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

@stop

@section('script')
<script src="{{ asset('js/imageSelection.js') }}"></script>
<script src="{{ asset('js/dynamicAddAndDelete.js') }}"></script>
<script src="{{ asset('js/contactDetailManager.js') }}"></script>
<script src="{{ asset('js/printEmployee.js') }}"></script>
<script src="{{ asset('js/employeePage.js') }}"></script>
@stop
