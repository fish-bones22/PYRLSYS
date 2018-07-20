@extends('layout.master')

@section('title')
{{ $employee->fullName != '' ? $employee->fullName : 'New Employee' }}
@stop

@section('content')


{{ session('error') }}
{{ session('success') }}

<div class="">
    <div class="d-inline-block">
        <div class="current-image">
            {{-- Use image select modal if updating --}}
            @if ($employee->id != 0)
            <button type="button" class="btn btn-light btn-change-image" data-toggle="modal" data-target="#selectPictureModal"><i class="fa fa-pencil"></i></button>
            {{-- Use file input if new employee --}}
            @else
            <input form="employeeForm" type="file" class="form-control-file btn-change-image" accept="image/*" name="new_image_file" />
            @endif
            <img src="{{ $employee->currentPicture == null ? asset('img/anom.png') : asset('storage/'.$employee->currentPicture['location'].$employee->currentPicture['filename']) }}" class="img-fluid" />
        </div>
    </div>
    <div class="d-inline-block">
        <div class="display-4">{{ $employee->fullName != '' ? $employee->fullName : 'New Employee' }}</div>
        <div class="lead">{{ $employee->employeeId != '' ? 'Employee ID: '.$employee->employeeId : '' }}</div>
        <div class="lead"></div>
    </div>
</div>


<form id="employeeForm" action="{{ action('EmployeeController@update', $employee->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('post')
    <input type="hidden" value="{{ $employee->id }}" />
    <input type="hidden" value="{{ $employee->employeeId }}" />
    <div class="row">
        <div class="col-md-4 col-sm-8">
            <div class="form-group">
                <label for="employeeId">ID:</label>
                <input id="employeeId" name="employeeId" type="text" class="form-control" value="{{ $employee->employeeId }}"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="firstname">First name:</label>
                <input id="firstname" name="firstName" type="text" class="form-control" value="{{ $employee->firstName }}"/>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="middlename">Middle name:</label>
                <input id="middlename" name="middleName" type="text" class="form-control" value="{{ $employee->middleName }}"/>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="lastname">Last name:</label>
                <input id="lastname" name="lastName" type="text" class="form-control" value="{{ $employee->lastName }}"/>
            </div>
        </div>
    </div>
    <div class="row">
        {{-- Phone numbers --}}
        <div class="col-sm-6" id="contactNumbersContainer">

            {{-- Primary contact --}}
            <div class="form-group">
                <label for="contactNumber">Primary Contact Number:</label>
                <input id="contactNumber" name="contact_number" type="text" class="form-control" value="{{ $employee->contactNumber }}"/>
            </div>

            {{-- Other Contact information --}}
            @if (sizeof($employee->otherContacts) != 0)

                <label for="contactNumber">Other Contact Number{{ sizeof($employee->otherContacts) > 1 ? 's' : '' }}:</label>
                {{-- Loop multiple contact details --}}
                @for ($i = 0; $i < sizeof($employee->otherContacts); $i++)

                <div class="form-group">
                    <input placeholder="Number" name="other_contacts[{{ $i }}][value]" type="text" class="form-control" value="{{ $employee->otherContacts[$i]['value'] }}"/>
                    <input placeholder="Details" name="other_contacts[{{ $i }}][detail]" type="text" class="form-control form-control-sm" value="{{ $employee->otherContacts[$i]['detail'] }}"/>
                    <input type="hidden" name="other_contacts[{{ $i }}][id]" value="{{ $employee->otherContacts[$i]['id'] }}"/>
                    <input type="hidden" name="other_contacts[{{ $i }}][key]" value="{{ $employee->otherContacts[$i]['key'] }}"/>
                    <input type="hidden" name="other_contacts[{{ $i }}][displayName]" value="{{ $employee->otherContacts[$i]['displayName'] }}"/>
                </div>

                @endfor
            @endif

            <div class="form-group" id="addNewContactButton">
                <input type="hidden" id="contactsSize" value="{{ sizeof($employee->otherContacts) }}" />
                <button type="button" class="btn btn-link" onclick="addContactDetails()">Add Contact Detail</button>
            </div>
        </div>

        {{-- Email addresses --}}
        <div class="col-sm-6">
            <div class="form-group">
                <label for="email">Email Address:</label>
                <input id="email" name="email" type="text" class="form-control" value="{{ $employee->email }}"/>
            </div>
        </div>
    </div>

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
                @if (sizeof($employee->pictures) != 0)
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
<script src="{{ asset('js/contactDetailManager.js') }}"></script>
@stop
