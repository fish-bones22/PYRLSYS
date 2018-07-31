@extends('layout.master')

@section('title')
Application For Employment
@stop

@section('content')

@if (session('error') != null)
<div class="alert alert-danger">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif
@if (session('success') != null)
<div class="alert alert-success">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif

<div class="mb-3">
    <div class="row align-items-end">
        <div class="col-sm-9">
            {{-- <div class="display-4">{{ $applicant->fullName != '' ? $applicant->fullName : 'New applicant' }}</div> --}}
        </div>
        <div class="col-sm-3">
            <div class="current-image float-md-right mx-auto">
                {{-- Use image select modal if updating --}}
                @if ($applicant->id != 0)
                <button type="button" class="btn btn-light btn-change-image" data-toggle="modal" data-target="#selectPictureModal"><i class="fa fa-pencil"></i></button>
                {{-- Use file input if new applicant --}}
                @else
                <input form="applicantForm" type="file" class="form-control-file btn-change-image" accept="image/*" name="new_image_file" />
                @endif
                <img src="{{ $applicant->currentPicture == null ? asset('img/anom.png') : asset('storage/'.$applicant->currentPicture['location'].$applicant->currentPicture['filename']) }}" class="img-fluid" />
            </div>
        </div>
    </div>
</div>


<form id="applicantForm" action="{{ action('applicantController@update', $applicant->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('post')
    <input type="hidden" name="id" value="{{ $applicant->id }}" />

    <div class="row">
        <div class="col-6 form-paper">
            <div class="form-group">
                <label for="position" class="form-paper-label">Position Applied for:</label>
                <input id="position" class="form-control" name="position" />
            </div>
        </div>
        <div class="col-6 form-paper">
            <div class="form-group">
                <label for="expectedsalary" class="form-paper-label">Expected salary:</label>
                <input id="expectedsalary" class="form-control" name="expected_salary" />
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <div class="form-check">
                    <input type="radio" name="respondent_to" value="Walk-in" id="walkIn" />
                    <label for="walkIn" class="form-paper-label">Walk-in</label>
                </div>
                <div class="form-check">
                    <input type="radio" name="respondent_to" value="Email" id="email" />
                    <label for="email" class="form-paper-label">Email</label>
                </div>
                <div class="form-check">
                    <input type="radio" name="respondent_to" value="Other" id="other" />
                    <label for="other" class="form-paper-label">Others:</label>
                    <input type="text" name="respondent_to_others" class="form-control form-control-sm" />
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <div class="form-check">
                    <input type="radio" name="respondent_to" value="Referral by CJI Employee" id="referral" />
                    <label for="referral" class="form-paper-label">Referral by CJI Employee</label>
                </div>
            </div>
            <div class="form-group">
                <input type="text" id="referralName" class="form-control form-control-sm" name="referral_name" placeholder="Name" />
                <input type="text" id="referralPosition" class="form-control form-control-sm" name="referral_position" placeholder="Position" />
            </div>
        </div>
    </div>

    <div class="form-group"><h5>Personal Information</h5></div>
    <div class="row">
        <div class="col-md-3 form-paper">
            <div class="form-group display-block-inline">
                <label for="lastname" class="form-paper-label">Last name:</label>
                <input id="lastname" name="last_name" type="text" class="form-control" value="{{ $applicant->lastName }}"/>
            </div>
        </div>
        <div class="col-md-3 form-paper">
            <div class="form-group display-block-inline">
                <label for="firstname" class="form-paper-label">First name:</label>
                <input id="firstname" name="first_name" type="text" class="form-control" value="{{ $applicant->firstName }}"/>
            </div>
        </div>
        <div class="col-md-3 form-paper">
            <div class="form-group display-block-inline">
                <label for="middlename" class="form-paper-label">Middle name:</label>
                <input id="middlename" name="middle_name" type="text" class="form-control" value="{{ $applicant->middleName }}"/>
            </div>
        </div>
        <div class="col-md-3 form-paper">
            <div class="form-group">
                <label for="maidenName" class="form-paper-label">Maiden Name (if married):</label>
                <input id="maidenName" name="maiden_name" type="text" class="form-control" value="{{ $applicant->maidenName }}"/>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-1 col-6 form-paper">
            <div class="form-group">
                <label for="age" class="form-paper-label">Age:</label>
                <input type="number" id="age" name="age" class="form-control" value="{{ $applicant->details['age']['value'] }}" />
            </div>
        </div>
        <div class="col-md-2 col-6 form-paper">
            <div class="form-group">
                <label for="dateOfBirth" class="form-paper-label">Date of Birth:</label>
                <input type="date" id="dateOfBirth" name="date_of_birth" class="form-control" value="{{ $applicant->details['dateOfBirth']['value'] }}" />
            </div>
        </div>
        <div class="col-md-2 col-6 form-paper">
            <div class="form-group">
                <label for="placeOfBirth" class="form-paper-label">Place of Birth:</label>
                <input type="text" id="placeOfBirth" name="place_of_birth" class="form-control" value="{{ $applicant->details['placeOfBirth']['value'] }}" />
            </div>
        </div>
        <div class="col-md-2 col-6 form-paper">
            <div class="form-group">
                <label for="citizenship" class="form-paper-label">Citizenship:</label>
                <input type="text" id="citizenship" name="citizenship" class="form-control" value="{{ $applicant->details['citizenship']['value'] }}" />
            </div>
        </div>
        <div class="col-md-2 col-6 form-paper">
            <div class="form-group">
                <label for="religion" class="form-paper-label">Religion:</label>
                <input type="text" id="religion" name="religion" class="form-control" value="{{ $applicant->details['religion']['value'] }}" />
            </div>
        </div>
        <div class="col-md-2 col-6 form-paper">
            <div class="form-group">
                <label for="civilStatus" class="form-paper-label">Civil Status:</label>
                <select id="civilStatus" name="civil_status" class="form-control"   >
                    <option value="single" {{ key_exists('civilstatus', $applicant->details) && $applicant->details['civilstatus']['value'] == 'single' ? 'selected' : ''}} >Single</option>
                    <option value="married" {{ key_exists('civilstatus', $applicant->details) && $applicant->details['civilstatus']['value'] == 'married' ? 'selected' : ''}} >Married</option>
                    <option value="widow" {{ key_exists('civilstatus', $applicant->details) && $applicant->details['civilstatus']['value'] == 'widow' ? 'selected' : ''}} >Widow</option>
                    <option value="separated"  {{ key_exists('civilstatus', $applicant->details) && $applicant->details['civilstatus']['value'] == 'separated' ? 'selected' : ''}} >Legally Separated</option>
                </select>
            </div>
        </div>
        <div class="col-md-1 col-6 form-paper">
            <div class="form-group">
                <label for="sex" class="form-paper-label">Sex:</label>
                <select id="sex" name="sex" class="form-control" value="{{ $applicant->sex }}">
                    <option value="m" {{ $applicant->sex == 'm' ? 'selected' : '' }}>Male</option>
                    <option value="f" {{ $applicant->sex == 'f' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 col-12">
            <div class="form-group">
                <label for="presentAddress" class="form-paper-label">Present Address:</label>
                <input id="presentAddress" name="present_address" class="form-control" value="{{ $applicant->details['presentAddress']['value'] }}" />
            </div>
        </div>
        <div class="col-md-2 col-12">
            <div class="form-group">
                <label for="presentAddressContact" class="form-paper-label">Tel/Mobile No.:</label>
                <input id="presentAddressContact" name="present_address_contact" class="form-control" value="{{ $applicant->details['presentAddressContact']['value'] }}" />
            </div>
        </div>
        <div class="col-md-10 col-12">
            <div class="form-group">
                <label for="permanentAddress" class="form-paper-label">Permanent Address:</label>
                <input id="permanentAddress" name="permanent_address" class="form-control" value="{{ $applicant->details['permanentAddress']['value'] }}" />
            </div>
        </div>
        <div class="col-md-2 col-12">
            <div class="form-group">
                <label for="permanentAddressContact" class="form-paper-label">Tel/Mobile No.:</label>
                <input id="permanentAddressContact" name="permanent_address_contact" class="form-control" value="{{ $applicant->details['permanentAddressContact']['value'] }}" />
            </div>
        </div>
        <div class="col-md-8 col-6">
            <div class="form-group">
                <label for="emailAddress" class="form-paper-label">Email Address:</label>
                <input type="email" id="emailAddress" name="email_address" class="form-control" value="{{ $applicant->details['emailAddress']['value'] }}" />
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="form-group">
                <label for="contactNumber" class="form-paper-label">Personal Contact No.:</label>
                <input id="contactNumber" name="contact_number" class="form-control" value="{{ $applicant->details['contact_number']['value'] }}" />
            </div>
        </div>
    </div>

    <div class="row education-0">
        <div class="col-md-2 form-paper">
            <div class="form-group">
                <label for="level[0]" class="form-paper-label">Level:</label>
                {{-- <input id="level[0]" name="level[0]" type="text" class="form-control" value="{{ key_exists('education', $applicant->details) ? $applicant->details['education'][0]['level']['value'] : '' }}" /> --}}
                <select id="level[0]" name="level[0]" class="form-control">
                    <option value="Post-Graduate">Post-Graduate</option>
                    <option value="College">College</option>
                    <option value="Vocational">Vocational</option>
                    <option value="High School">High School</option>
                    <option value="Elementary">Elementary</option>
                </select>
            </div>
        </div>
        <div class="col-md-3 form-paper">
            <div class="form-group">
                <label for="nameOfSchool[0]" class="form-paper-label">Name of School:</label>
                <input id="nameOfSchool[0]" name="name_of_school[0]" type="text" class="form-control" value="{{ key_exists('education', $applicant->details) ? $applicant->details['education'][0]['nameOfSchool']['value'] : '' }}" />
            </div>
        </div>
        <div class="col-md-3 form-paper">
            <div class="form-group">
                <label for="course[0]" class="form-paper-label">Course:</label>
                <input id="course[0]" name="course[0]" type="text" class="form-control" value="{{ key_exists('education', $applicant->details) ? $applicant->details['education'][0]['course']['value'] : '' }}" />
            </div>
        </div>
        <div class="col-md-1 form-paper">
            <div class="form-group">
                <label for="yearGraduated[0]" class="form-paper-label">Year Graduated:</label>
                <input id="yearGraduated[0]" name="year_graduated[0]" type="text" class="form-control" value="{{ key_exists('education', $applicant->details) ? $applicant->details['education'][0]['yearGraduated']['value'] : '' }}" />
            </div>
        </div>
        <div class="col-md-3 form-paper">
            <div class="form-group">
                <button type="button" class="close text-muted" data-index="0" onclick="deleteDependent(this)" tabindex="-1">&times;</button>
                <label for="recognition[0]" class="form-paper-label">Honors/Awards:</label>
                <input id="recognition[0]" name="recognition[0]" type="text" class="form-control" value="{{ key_exists('education', $applicant->details) ? $applicant->details['education'][0]['recognition']['value'] : '' }}" />
            </div>
        </div>
    </div>
    <div class="form-group" id="addEducationContainer"><button class="btn btn-link" type="button" onclick="addEducation()">Add Education</button></div>


    <div class="row examination-0">
        <div class="col-md-4 form-paper">
            <div class="form-group">
                <label for="titleOfExam[0]" class="form-paper-label">Title of Examination:</label>
                <input id="titleOfExam[0]" name="title_of_exam[0]" class="form-control" />
            </div>
        </div>
        <div class="col-md-3 form-paper">
            <div class="form-group">
                <label for="dateOfExam[0]" class="form-paper-label">Date of Examination:</label>
                <input id="dateOfExam[0]" name="date_of_exam[0]" type="date" class="form-control" value="{{ key_exists('examination', $applicant->details) ? $applicant->details['examination'][0]['date']['value'] : '' }}" />
            </div>
        </div>
        <div class="col-md-4 form-paper">
            <div class="form-group">
                <label for="placeOfExam[0]" class="form-paper-label">Place of Examination:</label>
                <input id="placeOfExam[0]" name="place_of_exam[0]" type="text" class="form-control" value="{{ key_exists('examination', $applicant->details) ? $applicant->details['examination'][0]['place']['value'] : '' }}" />
            </div>
        </div>
        <div class="col-md-1 form-paper">
            <div class="form-group">
                <label for="rating[0]" class="form-paper-label">Rating:</label>
                <input id="rating[0]" name="rating[0]" type="text" class="form-control" value="{{ key_exists('examination', $applicant->details) ? $applicant->details['examination'][0]['rating']['value'] : '' }}" />
            </div>
        </div>
    </div>
    <div class="form-group" id="addExaminationContainer"><button class="btn btn-link" type="button" onclick="addExamination()">Add Examination</button></div>

    <div class="row employement-record-0">
        <div class="col-md-1 form-paper">
            <div class="form-group">
                <label for="employmentRecordDateFrom[0]" class="form-paper-label">From:</label>
                <input id="employmentRecordDateFrom[0]" type="date" name="employment_record_date_from[0]" class="form-control" />
            </div>
        </div>
        <div class="col-md-1 form-paper">
            <div class="form-group">
                <label for="employmentRecordDateTo[0]" class="form-paper-label">To:</label>
                <input id="employmentRecordDateTo[0]" name="employment_record_date_to[0]" type="date" class="form-control" value="{{ key_exists('employmentRecord', $applicant->details) ? $applicant->details['employmentRecord'][0]['to']['value'] : '' }}" />
            </div>
        </div>
        <div class="col-md-2 form-paper">
            <div class="form-group">
                <label for="employmentRecordPosition[0]" class="form-paper-label">Position:</label>
                <input id="employmentRecordPosition[0]" name="employment_record_position[0]" type="text" class="form-control" value="{{ key_exists('employmentRecord', $applicant->details) ? $applicant->details['employmentRecord'][0]['position']['value'] : '' }}" />
            </div>
        </div>
        <div class="col-md-1 form-paper">
            <div class="form-group">
                <label for="employmentRecordStatus[0]" class="form-paper-label">Status:</label>
                <input id="employmentRecordStatus[0]" name="employment_record_status[0]" type="text" class="form-control" value="{{ key_exists('employmentRecord', $applicant->details) ? $applicant->details['employmentRecord'][0]['status']['value'] : '' }}" />
            </div>
        </div>
        <div class="col-md-2 form-paper">
            <div class="form-group">
                <label for="employmentRecordEmployer[0]" class="form-paper-label">Employer/Location:</label>
                <input id="employmentRecordEmployer[0]" name="employment_record_employer[0]" type="text" class="form-control" value="{{ key_exists('employmentRecord', $applicant->details) ? $applicant->details['employmentRecord'][0]['employer']['value'] : '' }}" />
            </div>
        </div>
        <div class="col-md-2 form-paper">
            <div class="form-group">
                <label for="employmentRecordSalary[0]" class="form-paper-label">Gross Monthly Salary:</label>
                <input id="employmentRecordSalary[0]" name="employment_record_salary[0]" type="text" class="form-control" value="{{ key_exists('employmentRecord', $applicant->details) ? $applicant->details['employmentRecord'][0]['salary']['value'] : '' }}" />
            </div>
        </div>
        <div class="col-md-2 form-paper">
            <div class="form-group">
                <label for="employmentRecordReasonForLeaving[0]" class="form-paper-label">Reason for Leaving:</label>
                <input id="employmentRecordReasonForLeaving[0]" name="employment_record_reason_for_leaving[0]" type="text" class="form-control" value="{{ key_exists('employmentRecord', $applicant->details) ? $applicant->details['employmentRecord'][0]['reasonForLeaving']['value'] : '' }}" />
            </div>
        </div>
    </div>
    <div class="form-group" id="addEmploymentRecordContainer"><button class="btn btn-link" type="button" onclick="addEmploymentRecord()">Add EmploymentRecord</button></div>

    <div class="row training-0">
        <div class="col-md-1 form-paper">
            <div class="form-group">
                <label for="traingingDateFrom[0]" class="form-paper-label">From:</label>
                <input id="traingingDateFrom[0]" type="date" name="training_date_from[0]" class="form-control" />
            </div>
        </div>
        <div class="col-md-1 form-paper">
            <div class="form-group">
                <label for="traingingDateTo[0]" class="form-paper-label">To:</label>
                <input id="traingingDateTo[0]" name="training_date_to[0]" type="date" class="form-control" value="{{ key_exists('training', $applicant->details) ? $applicant->details['training'][0]['from']['value'] : '' }}" />
            </div>
        </div>
        <div class="col-md-3 form-paper">
            <div class="form-group">
                <label for="traingingTitle[0]" class="form-paper-label">Title/Course:</label>
                <input id="traingingTitle[0]" name="training_title[0]" type="text" class="form-control" value="{{ key_exists('training', $applicant->details) ? $applicant->details['training'][0]['title']['value'] : '' }}" />
            </div>
        </div>
        <div class="col-md-4 form-paper">
            <div class="form-group">
                <label for="trainingVenue[0]" class="form-paper-label">Venue:</label>
                <input id="trainingVenue[0]" name="training_venue[0]" type="text" class="form-control" value="{{ key_exists('training', $applicant->details) ? $applicant->details['training'][0]['venue']['value'] : '' }}" />
            </div>
        </div>
        <div class="col-md-1 form-paper">
            <div class="form-group">
                <label for="trainingNumberOfHours[0]" class="form-paper-label">Number of Hours:</label>
                <input id="trainingNumberOfHours[0]" name="employment_record_employer[0]" type="number" class="form-control" value="{{ key_exists('training', $applicant->details) ? $applicant->details['training'][0]['hours']['value'] : '' }}" />
            </div>
        </div>
        <div class="col-md-2 form-paper">
            <div class="form-group">
                <label for="trainingOrganizer[0]" class="form-paper-label">Organizer/Sponsor:</label>
                <input id="trainingOrganizer[0]" name="training_organizer[0]" type="text" class="form-control" value="{{ key_exists('training', $applicant->details) ? $applicant->details['training'][0]['organizer']['value'] : '' }}" />
            </div>
        </div>
    </div>
    <div class="form-group" id="addEmploymentRecordContainer"><button class="btn btn-link" type="button" onclick="addEmploymentRecord()">Add EmploymentRecord</button></div>


    <div class="row">
        <div class="col-md-10 form-paper border-left">
            <div class="form-paper-section-label">Spouse (Maiden) Name</div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="spouseLastName" class="form-paper-label">Last name:</label>
                        <input id="spouseLastName" name="spouse_last_name[0]" type="text" class="form-control" value="{{ key_exists('spouse', $applicant->details) ? $applicant->details['spouse'][0]['lastname']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="spouseFirstName" class="form-paper-label">First name:</label>
                        <input id="spouseFirstName" name="spouse_first_name[0]" type="text" class="form-control" value="{{ key_exists('spouse', $applicant->details) ? $applicant->details['spouse'][0]['firstname']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="spouseMiddleName" class="form-paper-label">Middle name:</label>
                        <input id="spouseMiddleName" name="spouse_middle_name[0]" type="text" class="form-control" value="{{ key_exists('spouse', $applicant->details) ? $applicant->details['spouse'][0]['middlename']['value'] : '' }}" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 form-paper">
            <div class="form-group">
                <label for="spouseAge" class="form-paper-label">Age:</label>
                <
            </div>
        </div>
    </div>

    <?php

    // To ensure empty Dependent fields appear,
    // iterate at least one row even if $details['dependent'] is empty

    $limit = 1; // Store the number of dependents or use 1 if there are none.
    // If there is/are dependent/s use its size instead of the default 1.
    if (key_exists('dependent', $applicant->details) && is_array($applicant->details['dependent']) && sizeof($applicant->details['dependent']) > 0) {
        $limit = sizeof($applicant->details['dependent']);
    }

    ?>

    <input type="hidden" id="currentIndex" value="{{ $limit }}" />

    {{-- Use the $limit variable for looping --}}
    @for ($i = 0; $i < $limit; $i++)

    <div class="row dependent-{{$i}}">

        <div class="col-md-8 form-paper border-left">

            @if ($i == 0)
            {{-- Show section label and Pluralize --}}
            <div class="form-paper-section-label">Dependent{{ $limit > 1 ? 's' : '' }}</div>
            @endif

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="dependentLastName[{{$i}}]" class="form-paper-label">Last name:</label>
                        <input id="dependentLastName[{{$i}}]" name="dependent_last_name[{{$i}}]" type="text" class="form-control" value="{{ key_exists('dependent', $applicant->details) ? $applicant->details['dependent'][$i]['lastname']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="dependentFirstName[{{$i}}]" class="form-paper-label">First name:</label>
                        <input id="dependentFirstName[{{$i}}]" name="dependent_first_name[{{$i}}]" type="text" class="form-control" value="{{ key_exists('dependent', $applicant->details) ? $applicant->details['dependent'][$i]['firstname']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="dependentMiddleName[{{$i}}]" class="form-paper-label">Middle name:</label>
                        <input id="dependentMiddleName[{{$i}}]" name="dependent_middle_name[{{$i}}]" type="text" class="form-control" value="{{ key_exists('dependent', $applicant->details) ? $applicant->details['dependent'][$i]['middlename']['value'] : '' }}" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 form-paper">
            <div class="form-group">
                <button type="button" class="close text-muted" data-index="{{$i}}" onclick="deleteDependent(this)" tabindex="-1">&times;</button>
                <label for="dependentRelationship[{{$i}}]" class="form-paper-label">Relationship:</label>
                <input id="dependentRelationship[{{$i}}]" name="dependent_relationship[{{$i}}]" type="text" class="form-control" value="{{ key_exists('dependent', $applicant->details) ? $applicant->details['dependent'][$i]['relationship']['value'] : '' }}" />
            </div>
        </div>

    </div>

    @endfor

    <div class="form-group" id="addDependentContainer"><button class="btn btn-link" type="button" onclick="addDependent()">Add Dependent</button></div>


    <div class="form-group"><h5>Employment Information</h5></div>

    <div class="row">
        <div class="col-md-2 col-5 form-paper">
            <div class="form-group">
                <label for="timeCard" class="form-paper-label">Time Card #</label>
                <input id="timeCard" type="text" name="time_card" class="form-control" value="{{ key_exists('timecard', $applicant->details) ? $applicant->details['timecard']['value'] : '' }}" />
            </div>
        </div>
        <div class="col-md-3 col-7 form-paper">
            <div class="form-group">
                <label for="department" class="form-paper-label">Department/Project</label>
                <select id="department" type="text" name="department" class="form-control" value="{{ key_exists('department', $applicant->employmentDetails) ? $applicant->employmentDetails['department'] : '' }}" />
                    @foreach($categories['department'] as $category)
                    <option value="{{ $category->id }}" {{ key_exists('department', $applicant->employmentDetails) && $applicant->employmentDetails['department'] == $category->id ? 'selected' : '' }} >{{ $category->value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4 col-7 form-paper">
            <div class="form-group">
                <label for="position" class="form-paper-label">Position</label>
                <input id="position" type="text" name="position" class="form-control" value="{{ key_exists('position', $applicant->details) ? $applicant->details['position']['value'] : '' }}" />
            </div>
        </div>
        <div class="col-md-3 col-5 form-paper">
            <div class="form-group">
                <label for="employmentType" class="form-paper-label">Employment Type</label>
                <select id="employmentType" type="text" name="employment_type" class="form-control" value="{{ key_exists('employmenttype', $applicant->employmentDetails) ? $applicant->employmentDetails['employmenttype']['value'] : '' }}" />
                    @foreach($categories['employmenttype'] as $category)
                    <option value="{{ $category->id }}" {{ key_exists('employmenttype', $applicant->employmentDetails) && $applicant->employmentDetails['employmenttype'] == $category->id ? 'selected' : '' }} >{{ $category->value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2 col-4 form-paper">
            <div class="form-group">
                <label for="dateHired" class="form-paper-label">Date Hired</label>
                <input id="dateHired" type="date" name="date_hired" class="form-control" value="{{ key_exists('datehired', $applicant->details) ? $applicant->details['datehired']['value'] : '' }}" />
            </div>
        </div>
        <div class="col-md-2 col-4 form-paper">
            <div class="form-group">
                <label for="dateEnded" class="form-paper-label">Until</label>
                <input id="dateEnded" type="date" name="date_end" class="form-control" value="{{ key_exists('dateend', $applicant->details) ? $applicant->details['dateend']['value'] : '' }}" />
            </div>
        </div>
        <div class="col-md-3 col-4 form-paper">
            <div class="form-group">
                <label for="status" class="form-paper-label">Status</label>
                <select id="status" name="contract_status" class="form-control" value="{{ key_exists('contractstatus', $applicant->employmentDetails) ? $applicant->employmentDetails['contractstatus']['value'] : '' }}" />
                    @foreach($categories['contractstatus'] as $category)
                    <option value="{{ $category->id }}" {{ key_exists('contractstatus', $applicant->employmentDetails) && $applicant->employmentDetails['contractstatus'] == $category->id ? 'selected' : '' }} >{{ $category->value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-5 form-paper">
            <label class="form-paper-label">Benefits:</label><br/>
            <div class="form-group">
                <div class="form-check-inline">
                    <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" name="tin" {{ key_exists('tin', $applicant->deductibles) ? 'checked' : '' }} />
                        TIN
                    </label>
                </div>
                <div class="form-check-inline">
                    <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" name="sss" {{ key_exists('sss', $applicant->deductibles) ? 'checked' : '' }} />
                        SSS
                    </label>
                </div>
                <div class="form-check-inline">
                    <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" name="philhealth" {{ key_exists('philhealth', $applicant->deductibles) ? 'checked' : '' }} />
                        PhilHealth
                    </label>
                </div>
                <div class="form-check-inline">
                    <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" name="pagibig" {{ key_exists('pagibig', $applicant->deductibles) ? 'checked' : '' }} />
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
                <input id="tin" type="text" class="form-control" name="tinnumber" value="{{ key_exists('tin', $applicant->deductibles) ? $applicant->deductibles['tin'] : '' }}" />
            </div>
        </div>
        <div class="col-md form-paper">
            <div class="form-group">
                <label for="ssn" class="form-paper-label">SS #</label>
                <input id="ssn" type="text" class="form-control" name="ssnumber" value="{{ key_exists('sss', $applicant->deductibles) ? $applicant->deductibles['sss'] : '' }}" />
            </div>
        </div>
        <div class="col-md form-paper">
            <div class="form-group">
                <label for="philhealth" class="form-paper-label">PhilHealth</label>
                <input id="philhealth" type="text" class="form-control" name="philhealthnumber" value="{{ key_exists('philhealth', $applicant->deductibles) ? $applicant->deductibles['philhealth'] : '' }}" />
            </div>
        </div>
        <div class="col-md form-paper">
            <div class="form-group">
                <label for="pagibig" class="form-paper-label">PAGIBIG</label>
                <input id="pagibig" type="text" class="form-control" name="pagibignumber" value="{{ key_exists('pagibig', $applicant->deductibles) ? $applicant->deductibles['pagibig'] : '' }}" />
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2 form-paper">
            <div class="form-group">
                <label for="typeOfPayment" class="form-paper-label">Type of payment</label>
                <select id="typeOfPayment" name="payment_type" class="form-control" value="{{ key_exists('paymenttype', $applicant->employmentDetails) ? $applicant->employmentDetails['paymenttype']['value'] : '' }}" />
                    @foreach($categories['paymenttype'] as $category)
                    <option value="{{ $category->id }}" {{ key_exists('paymenttype', $applicant->employmentDetails) && $applicant->employmentDetails['paymenttype'] == $category->id ? 'selected' : '' }} >{{ $category->value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2 form-paper">
            <div class="form-group">
                <label for="modeOfPayment" class="form-paper-label">Mode of payment</label>
                <select id="modeOfPayment" name="payment_mode" class="form-control" value="{{ key_exists('paymentmode', $applicant->employmentDetails) ? $applicant->employmentDetails['paymentmode']['value'] : '' }}" />
                    @foreach($categories['paymentmode'] as $category)
                    <option value="{{ $category->id }}" {{ key_exists('paymentmode', $applicant->employmentDetails) && $applicant->employmentDetails['paymentmode'] == $category->id ? 'selected' : '' }} >{{ $category->value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4 form-paper">
            <div class="form-group">
                <label for="ratePerHour" class="form-paper-label">Hourly rate</label>
                <input id="ratePerHour" type="number" name="rate" step="0.05" class="form-control" value="{{ key_exists('rate', $applicant->details) ? $applicant->details['rate']['value'] : '' }}" />
            </div>
        </div>
        <div class="col-md-4 form-paper">
            <div class="form-group">
                <label for="allowance" class="form-paper-label">Allowance</label>
                <input id="allowance" type="number" name="allowance" step="0.05" class="form-control" value="{{ key_exists('allowance', $applicant->details) ? $applicant->details['allowance']['value'] : '' }}" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2 form-paper">
            <div class="form-group">
                <label for="memo" class="form-paper-label">Number of Memo</label>
                <input id="memo" type="number" name="number_of_memo" class="form-control"  value="{{ key_exists('numberofmemo', $applicant->details) ? $applicant->details['numberofmemo']['value'] : '' }}" />
            </div>
        </div>
        <div class="col-md-10 form-paper">
            <div class="form-group">
                <label for="remarks" class="form-paper-label">Remarks</label>
                <input id="remarks" type="text" name="remarks" class="form-control" value="{{ key_exists('remarks', $applicant->details ) ? $applicant->details['remarks']['value'] : '' }}" />
            </div>
        </div>
    </div>
        {{-- <div class="row">
        {{-- Phone numbers
        <div class="col-sm-6 form-paper" id="contactNumbersContainer">

            {{-- Primary contact
            <div class="form-group">
                <label for="contactNumber">Primary Contact Number:</label>
                <input id="contactNumber" name="contact_number" type="text" class="form-control" value="{{ $applicant->contactNumber }}"/>
            </div>

            {{-- Other Contact information
            @if (sizeof($applicant->otherContacts) != 0)

                <label for="contactNumber">Other Contact Number{{ sizeof($applicant->otherContacts) > 1 ? 's' : '' }}:</label>
                {{-- Loop multiple contact details
                @for ($i = 0; $i < sizeof($applicant->otherContacts); $i++)

                <div class="form-group">
                    <input placeholder="Number" name="other_contacts[{{ $i }}][value]" type="text" class="form-control" value="{{ $applicant->otherContacts[$i]['value'] }}"/>
                    <input placeholder="Details" name="other_contacts[{{ $i }}][detail]" type="text" class="form-control form-control-sm" value="{{ $applicant->otherContacts[$i]['detail'] }}"/>
                    <input type="hidden" name="other_contacts[{{ $i }}][id]" value="{{ $applicant->otherContacts[$i]['id'] }}"/>
                    <input type="hidden" name="other_contacts[{{ $i }}][key]" value="{{ $applicant->otherContacts[$i]['key'] }}"/>
                    <input type="hidden" name="other_contacts[{{ $i }}][displayName]" value="{{ $applicant->otherContacts[$i]['displayName'] }}"/>
                </div>

                @endfor
            @endif

            <div class="form-group" id="addNewContactButton">
                <input type="hidden" id="contactsSize" value="{{ sizeof($applicant->otherContacts) }}" />
                <button type="button" class="btn btn-link" onclick="addContactDetails()">Add Contact Detail</button>
            </div>
        </div>

        {{-- Email addresses
        <div class="col-sm-6 form-paper">
            <div class="form-group">
                <label for="email">Email Address:</label>
                <input id="email" name="email" type="text" class="form-control" value="{{ $applicant->email }}"/>
            </div>
        </div>
    </div> --}}
    <div class="m-4">&nbsp;</div>
    <div class="fixed-bottom btn-container m-4">
        <div class="float-right">
            <div class="btn-group">
                <a class="btn btn-light" href="{{ action('applicantController@index') }}">Back to List</a>
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
                @if ($applicant->id != 0)
                <form id="imageForm" action="{{ action('applicantController@updateImage', $applicant->id) }}" method="POST" enctype="multipart/form-data">
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
                @if (is_array($applicant->pictures) && sizeof($applicant->pictures) != 0)
                <div class="lead">Previous Images</div>
                    <?php $index = 0 ?>
                    @foreach($applicant->pictures as $pic)

                        @if ($index % 4 == 0)
                        <div class="row mb-4">
                        @endif

                        <div class="col-sm-3 col-6 previous-image" data-location="{{ $pic['location'] }}" data-filename={{  $pic['filename'] }}>
                            <form action="{{ action('applicantController@deleteImage', $applicant->id) }}" method="POST" >
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
<script src="{{ asset('js/applicantPage.js') }}"></script>
@stop
