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

<div class="row">
    <div class="col-md-8 offset-md-2">

        <form id="applicantForm" action="{{ action('ApplicantController@update', $applicant->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('post')
            <input type="hidden" name="id" value="{{ $applicant->id }}" />

           <div class="row">
                <div class="col-12 text-center form-paper">
                    <div class="display-4">Application Form</div>
                    {{-- <div class="display-4">{{ $applicant->fullName != '' ? $applicant->fullName : 'New applicant' }}</div> --}}
                </div>
           </div>
            <div class="row">
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="position" class="form-paper-label">Position Applied for:</label>
                        <input id="position" class="form-control" name="position" value="{{ key_exists('position', $applicant->details) ? $applicant->details['position']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="expectedsalary" class="form-paper-label">Expected salary:</label>
                        <input id="expectedsalary" type="number" step="0.05" class="form-control" name="expected_salary" value="{{ key_exists('expectedsalary', $applicant->details) ? $applicant->details['expectedsalary']['value'] : '' }}" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 form-paper section-title">Basic Information</div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="sex" class="form-paper-label">Sex:</label>
                        <select id="sex" name="sex" class="form-control" value="{{ $applicant->sex }}">
                            <option value="m" {{ $applicant->sex == 'm' ? 'selected' : '' }}>Male</option>
                            <option value="f" {{ $applicant->sex == 'f' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                </div>
                <div class="col-6 form-paper">
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
                <div class="col-12 form-paper">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="lastname" class="form-paper-label">Last name:</label>
                                <input id="lastname" name="last_name" type="text" class="form-control" value="{{ $applicant->lastName }}"/>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="firstname" class="form-paper-label">First name:</label>
                                <input id="firstname" name="first_name" type="text" class="form-control" value="{{ $applicant->firstName }}"/>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="middlename" class="form-paper-label">Middle name:</label>
                                <input id="middlename" name="middle_name" type="text" class="form-control" value="{{ $applicant->middleName }}"/>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="maidenName" class="form-paper-label">Maiden Name (if married):</label>
                                <input id="maidenName" name="maiden_name" type="text" class="form-control" value="{{ $applicant->maidenName }}"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 form-paper section-title">Respondent to:</div>
                <div class="col-12 form-paper">
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
                            <label for="other" class="form-paper-label">Others</label>
                            <input placeholder="specify" type="text" name="respondent_to_others" class="form-control form-control-sm" />
                        </div>
                        <div class="form-check">
                            <input type="radio" name="respondent_to" value="Referral by CJI Employee" id="referral" />
                            <label for="referral" class="form-paper-label">Referral by CJI Employee</label>
                            <input type="text" id="referralName" class="form-control form-control-sm" name="referral_name" placeholder="Name" />
                            <input type="text" id="referralPosition" class="form-control form-control-sm" name="referral_position" placeholder="Position" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 form-paper section-title">Select Image</div>
                <div class="col-12 form-paper">
                    <div class="current-image mx-auto m-2">
                        <input form="applicantForm" type="file" class="form-control-file btn-change-image" accept="image/*" name="new_image_file" />
                        <img src="{{ $applicant->currentPicture == null ? asset('img/anom.png') : asset('storage/'.$applicant->currentPicture['location'].$applicant->currentPicture['filename']) }}" class="img-fluid" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 form-paper section-title">Personal Information</div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label for="dateOfBirth" class="form-paper-label">Date of Birth:</label>
                        <input type="date" id="dateOfBirth" name="date_of_birth" class="form-control" value="{{ key_exists('dateofbirth', $applicant->details) ? $applicant->details['dateofbirth']['value'] : '' }}"/>
                    </div>
                </div>
                <div class="col-3 form-paper">
                    <div class="form-group">
                        <label for="age" class="form-paper-label">Age:</label>
                        <input type="number" id="age" name="age" class="form-control" value="{{ key_exists('age', $applicant->details) ? $applicant->details['age']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-5 form-paper">
                    <div class="form-group">
                        <label for="placeOfBirth" class="form-paper-label">Place of Birth:</label>
                        <input type="text" id="placeOfBirth" name="place_of_birth" class="form-control"  value="{{ key_exists('placeofbirth', $applicant->details) ? $applicant->details['placeofbirth']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="citizenship" class="form-paper-label">Citizenship:</label>
                        <input type="text" id="citizenship" name="citizenship" class="form-control" value="{{ key_exists('citizenship', $applicant->details) ? $applicant->details['citizenship']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="religion" class="form-paper-label">Religion:</label>
                        <input type="text" id="religion" name="religion" class="form-control"  value="{{ key_exists('religion', $applicant->details) ? $applicant->details['religion']['value'] : '' }}" />
                    </div>
                </div>

                <div class="col-8 form-paper">
                    <div class="form-group">
                        <label for="presentAddress" class="form-paper-label">Present Address:</label>
                        <input id="presentAddress" name="present_address" class="form-control" value="{{ key_exists('presentaddress', $applicant->details) ? $applicant->details['presentaddress']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label for="presentAddressContact" class="form-paper-label">Tel/Mobile No.:</label>
                        <input id="presentAddressContact" name="present_address_contact" class="form-control" value="{{ key_exists('presentaddresscontact', $applicant->details) ? $applicant->details['presentaddresscontact']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-8 form-paper">
                    <div class="form-group">
                        <label for="permanentAddress" class="form-paper-label">Permanent Address:</label>
                        <input id="permanentAddress" name="permanent_address" class="form-control" value="{{ key_exists('permanentaddress', $applicant->details) ? $applicant->details['permanentaddress']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label for="permanentAddressContact" class="form-paper-label">Tel/Mobile No.:</label>
                        <input id="permanentAddressContact" name="permanent_address_contact" class="form-control" value="{{ key_exists('permanentaddresscontact', $applicant->details) ? $applicant->details['permanentaddresscontact']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-8 form-paper">
                    <div class="form-group">
                        <label for="emailAddress" class="form-paper-label">Email Address:</label>
                        <input type="email" id="emailAddress" name="email_address" class="form-control" value="{{ key_exists('emailaddress', $applicant->details) ? $applicant->details['emailaddress']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label for="contactNumber" class="form-paper-label">Personal Contact No.:</label>
                        <input id="contactNumber" name="contact_number" class="form-control" value="{{ key_exists('presentaddresscontact', $applicant->details) ? $applicant->details['presentaddresscontact']['value'] : '' }}" />
                    </div>
                </div>
            </div>

            <input type="hidden" id="education-index" value="1" />
            <div class="row"><div class="col-12 form-paper section-title">Educational Attainment</div></div>
            <div class="row education-0">
                <div class="col-12 form-paper">
                    <div class="form-group">
                        <button type="button" class="close text-muted" data-index="0" onclick="deleteRow(this, 'education')" tabindex="-1">&times;</button>
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
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="nameOfSchool[0]" class="form-paper-label">Name of School:</label>
                        <input id="nameOfSchool[0]" name="name_of_school[0]" type="text" class="form-control" value="{{ key_exists('education', $applicant->details) ? $applicant->details['education'][0]['nameofschool']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="course[0]" class="form-paper-label">Course:</label>
                        <input id="course[0]" name="course[0]" type="text" class="form-control" value="{{ key_exists('education', $applicant->details) ? $applicant->details['education'][0]['course']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-5 form-paper border-bottom">
                    <div class="form-group">
                        <label for="yearGraduated[0]" class="form-paper-label">Year Graduated:</label>
                        <input id="yearGraduated[0]" name="year_graduated[0]" type="text" class="form-control" value="{{ key_exists('education', $applicant->details) ? $applicant->details['education'][0]['yeargraduated']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-7 form-paper border-bottom">
                    <div class="form-group">
                        <label for="recognition[0]" class="form-paper-label">Honors/Awards:</label>
                        <input id="recognition[0]" name="recognition[0]" type="text" class="form-control" value="{{ key_exists('education', $applicant->details) && key_exists('recognition', $applicant->details['education']) ? $applicant->details['education'][0]['recognition']['value'] : '' }}" />
                    </div>
                </div>
            </div>
            <div class="row addContainer" id="addEducationContainer" >
                <div class="col-12 form-paper"><button class="btn btn-link" type="button" onclick="createNewRow(this, 'education')">Add Education</button></div>
            </div>


            <input type="hidden" id="examination-index" value="1" />
            <div class="row examination-0">
                <div class="col-12 form-paper section-title">Examinations</div>
                <div class="col-7 form-paper">
                    <div class="form-group">
                        <label for="titleOfExam[0]" class="form-paper-label">Title of Examination:</label>
                        <input id="titleOfExam[0]" name="title_of_exam[0]" class="form-control" value="{{ key_exists('examination', $applicant->details) ? $applicant->details['examination'][0]['titleofexam']['value'] : '' }}"/>
                    </div>
                </div>
                <div class="col-5 form-paper">
                    <div class="form-group">
                        <button type="button" class="close text-muted" data-index="0" onclick="deleteRow(this, 'examination')" tabindex="-1">&times;</button>
                        <label for="dateOfExam[0]" class="form-paper-label">Date of Examination:</label>
                        <input id="dateOfExam[0]" name="date_of_exam[0]" type="date" class="form-control" value="{{ key_exists('examination', $applicant->details) ? $applicant->details['examination'][0]['dateofexam']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-7 form-paper border-bottom">
                    <div class="form-group">
                        <label for="placeOfExam[0]" class="form-paper-label">Place of Examination:</label>
                        <input id="placeOfExam[0]" name="place_of_exam[0]" type="text" class="form-control" value="{{ key_exists('examination', $applicant->details) ? $applicant->details['examination'][0]['placeofexam']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-5 form-paper border-bottom">
                    <div class="form-group">
                        <label for="rating[0]" class="form-paper-label">Rating:</label>
                        <input id="rating[0]" name="rating[0]" type="text" class="form-control" value="{{ key_exists('examination', $applicant->details) ? $applicant->details['examination'][0]['rating']['value'] : '' }}" />
                    </div>
                </div>
            </div>
            <div class="row addContainer" id="addExaminationContainer">
                <div class="col-12 form-paper"><button class="btn btn-link" type="button" onclick="createNewRow(this, 'examination')">Add Examination</button></div>
            </div>

            <input type="hidden" id="employment-record-index" value="1" />
            <div class="row">
                <div class="col-12 form-paper section-title">Employment History</div>
            </div>
            <div class="row employment-record-0">
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="employmentRecordDateFrom[0]" class="form-paper-label">From:</label>
                        <input id="employmentRecordDateFrom[0]" type="date" name="employment_record_date_from[0]" class="form-control" />
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <button type="button" class="close text-muted" data-index="0" onclick="deleteRow(this, 'employment-record')" tabindex="-1">&times;</button>
                        <label for="employmentRecordDateTo[0]" class="form-paper-label">To:</label>
                        <input id="employmentRecordDateTo[0]" name="employment_record_date_to[0]" type="date" class="form-control" value="{{ key_exists('employmentrecord', $applicant->details) ? $applicant->details['employmentrecord'][0]['dateto']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-7 form-paper">
                    <div class="form-group">
                        <label for="employmentRecordPosition[0]" class="form-paper-label">Position:</label>
                        <input id="employmentRecordPosition[0]" name="employment_record_position[0]" type="text" class="form-control" value="{{ key_exists('employmentrecord', $applicant->details) ? $applicant->details['employmentrecord'][0]['position']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-5 form-paper">
                    <div class="form-group">
                        <label for="employmentRecordStatus[0]" class="form-paper-label">Status:</label>
                        <input id="employmentRecordStatus[0]" name="employment_record_status[0]" type="text" class="form-control" value="{{ key_exists('employmentRecord', $applicant->details) ? $applicant->details['employmentRecord'][0]['status']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-12 form-paper">
                    <div class="form-group">
                        <label for="employmentRecordEmployer[0]" class="form-paper-label">Employer/Location:</label>
                        <input id="employmentRecordEmployer[0]" name="employment_record_employer[0]" type="text" class="form-control" value="{{ key_exists('employmentRecord', $applicant->details) ? $applicant->details['employmentRecord'][0]['employer']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="employmentRecordSalary[0]" class="form-paper-label">Gross Monthly Salary:</label>
                        <input id="employmentRecordSalary[0]" name="employment_record_salary[0]" type="text" class="form-control" value="{{ key_exists('employmentRecord', $applicant->details) ? $applicant->details['employmentRecord'][0]['salary']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="employmentRecordReasonForLeaving[0]" class="form-paper-label">Reason for Leaving:</label>
                        <input id="employmentRecordReasonForLeaving[0]" name="employment_record_reason_for_leaving[0]" type="text" class="form-control" value="{{ key_exists('employmentRecord', $applicant->details) ? $applicant->details['employmentRecord'][0]['reasonForLeaving']['value'] : '' }}" />
                    </div>
                </div>
            </div>
            <div class="row addContainer" id="addEmploymentRecordContainer">
                <div class="col-12 form-paper"><button class="btn btn-link" type="button" onclick="createNewRow(this, 'employment-record')">Add Employment Record</button></div>
            </div>

            <input type="hidden" id="training-index" value="1" />
            <div class="row training-0">
                <div class="col-12 form-paper section-title">Trainings and Seminars</div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="traingingDateFrom[0]" class="form-paper-label">From:</label>
                        <input id="traingingDateFrom[0]" type="date" name="training_date_from[0]" class="form-control" value="{{ key_exists('training', $applicant->details) ? $applicant->details['training'][0]['datefrom']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <button type="button" class="close text-muted" data-index="0" onclick="deleteRow(this, 'training')" tabindex="-1">&times;</button>
                        <label for="traingingDateTo[0]" class="form-paper-label">To:</label>
                        <input id="traingingDateTo[0]" name="training_date_to[0]" type="date" class="form-control" value="{{ key_exists('training', $applicant->details) ? $applicant->details['training'][0]['dateto']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-12 form-paper">
                    <div class="form-group">
                        <label for="traingingTitle[0]" class="form-paper-label">Title/Course:</label>
                        <input id="traingingTitle[0]" name="training_title[0]" type="text" class="form-control" value="{{ key_exists('training', $applicant->details) ? $applicant->details['training'][0]['title']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-8 form-paper">
                    <div class="form-group">
                        <label for="trainingVenue[0]" class="form-paper-label">Venue:</label>
                        <input id="trainingVenue[0]" name="training_venue[0]" type="text" class="form-control" value="{{ key_exists('training', $applicant->details) ? $applicant->details['training'][0]['venue']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label for="trainingNumberOfHours[0]" class="form-paper-label">Hours:</label>
                        <input id="trainingNumberOfHours[0]" name="training_hours[0]" type="number" class="form-control" value="{{ key_exists('training', $applicant->details) ? $applicant->details['training'][0]['hours']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-12 form-paper border-bottom">
                    <div class="form-group">
                        <label for="trainingOrganizer[0]" class="form-paper-label">Organizer/Sponsor:</label>
                        <input id="trainingOrganizer[0]" name="training_organizer[0]" type="text" class="form-control" value="{{ key_exists('training', $applicant->details) ? $applicant->details['training'][0]['organizer']['value'] : '' }}" />
                    </div>
                </div>
            </div>
            <div class="row addContainer" id="addTrainingContainer">
                <div class="col-12 form-paper">
                    <button class="btn btn-link" type="button" onclick="createNewRow(this, 'training')">Add Training</button>
                </div>
            </div>


            <div class="row">
                <div class="col-12 form-paper section-title">Family Background</div>
                <div class="col-12 form-paper section-label">Spouse (Maiden) Name</div>
                <div class="col-12 form-paper border-left">
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
            </div>
            <div class="row">
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label for="spouseAge" class="form-paper-label">Age:</label>
                        <input id="spouseAge" name="spouse_age[0]" type="number" class="form-control" value="{{ key_exists('spouse', $applicant->details) ? $applicant->details['spouse'][0]['age']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-8 form-paper">
                    <div class="form-group">
                        <label for="spouseAddress" class="form-paper-label">Address:</label>
                        <input id="spouseAddress" name="spouse_address[0]" type="text" class="form-control" value="{{ key_exists('spouse', $applicant->details) ? $applicant->details['spouse'][0]['address']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-5 form-paper">
                    <div class="form-group">
                        <label for="dateOfMarriage" class="form-paper-label">Date of Marriage:</label>
                        <input id="dateOfMarriage" name="date_of_marriage[0]" type="date" class="form-control" value="{{ key_exists('spouse', $applicant->details) ? $applicant->details['spouse'][0]['dateofmarriage']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-7 form-paper">
                    <div class="form-group">
                        <label for="placeOfMarriage" class="form-paper-label">Place of Marriage:</label>
                        <input id="placeOfMarriage" name="place_of_marriage[0]" type="text" class="form-control" value="{{ key_exists('spouse', $applicant->details) ? $applicant->details['spouse'][0]['placeofmarriage']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-5 form-paper">
                    <div class="form-group">
                        <label for="occupationOfSpouse" class="form-paper-label">Occupation of Spouse:</label>
                        <input id="occupationOfSpouse" name="occupation_of_spouse[0]" type="text" class="form-control" value="{{ key_exists('spouse', $applicant->details) ? $applicant->details['spouse'][0]['occupation']['value'] : '' }}"/>
                    </div>
                </div>
                <div class="col-7 form-paper">
                    <div class="form-group">
                        <label for="employerOfSpouse" class="form-paper-label">Employer of Spouse:</label>
                        <input id="employerOfSpouse" name="employer_of_spouse[0]" type="text" class="form-control" value="{{ key_exists('spouse', $applicant->details) ? $applicant->details['spouse'][0]['employer']['value'] : '' }}" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-10 form-paper">
                <div class="section-label">Mother's Maiden Name</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="motherLastName" class="form-paper-label">Last name:</label>
                                <input id="motherLastName" name="mother_last_name" type="text" class="form-control" value="{{ key_exists('mother', $applicant->details) ? $applicant->details['mother']['lastname']['value'] : '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="motherFirstName" class="form-paper-label">First name:</label>
                                <input id="motherFirstName" name="mother_first_name" type="text" class="form-control" value="{{ key_exists('mother', $applicant->details) ? $applicant->details['mother']['firstname']['value'] : '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="motherMiddleName" class="form-paper-label">Middle name:</label>
                                <input id="motherMiddleName" name="mother_middle_name" type="text" class="form-control" value="{{ key_exists('mother', $applicant->details) ? $applicant->details['mother']['middlename']['value'] : '' }}" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 form-paper">
                    <div class="form-group">
                        <label for="motherAge" class="form-paper-label">Age</label>
                        <input id="motherAge" name="mother_age" class="form-control" type="number" value="{{ key_exists('mother', $applicant->details) ? $applicant->details['mother']['age']['value'] : '' }}" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-10 form-paper">
                    <div class="section-label">Father's Name</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fatherLastName" class="form-paper-label">Last name:</label>
                                <input id="fatherLastName" name="father_last_name" type="text" class="form-control" value="{{ key_exists('father', $applicant->details) ? $applicant->details['father']['lastname']['value'] : '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fatherFirstName" class="form-paper-label">First name:</label>
                                <input id="fatherFirstName" name="father_first_name" type="text" class="form-control" value="{{ key_exists('father', $applicant->details) ? $applicant->details['father']['firstname']['value'] : '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fatherMiddleName" class="form-paper-label">Middle name:</label>
                                <input id="fatherMiddleName" name="father_middle_name" type="text" class="form-control" value="{{ key_exists('father', $applicant->details) ? $applicant->details['father']['middlename']['value'] : '' }}" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 form-paper">
                    <div class="form-group">
                        <label for="fatherAge" class="form-paper-label">Age</label>
                        <input id="fatherAge" name="father_age" class="form-control" type="number" value="{{ key_exists('father', $applicant->details) ? $applicant->details['father']['age']['value'] : '' }}" />
                    </div>
                </div>
            </div>

            <input type="hidden" id="child-index" value="1" />
            <div class="row">
                <div class="col-12 form-paper section-label">Children</div>
            </div>
            <div class="row child-0">
                <div class="col-12 form-paper">
                    <button type="button" class="close text-muted" data-index="0" onclick="deleteRow(this, 'child')" tabindex="-1">&times;</button>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="childLastName[0]" class="form-paper-label">Last Name:</label>
                                <input id="childLastName[0]" class="form-control" name="child_last_name[0]" type="text" value="{{ key_exists('child', $applicant->details) ? $applicant->details['child'][0]['lastname']['value'] : '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="childFirstName[0]" class="form-paper-label">First Name:</label>
                                <input id="childFirstName[0]" class="form-control" name="child_first_name[0]" type="text" value="{{ key_exists('child', $applicant->details) ? $applicant->details['child'][0]['firstname']['value'] : '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="childMiddleName[0]" class="form-paper-label">Middle Name:</label>
                                <input id="childMiddleName[0]" class="form-control" name="child_middle_name[0]" type="text" value="{{ key_exists('child', $applicant->details) ? $applicant->details['child'][0]['middlename']['value'] : '' }}" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="childSex[0]" class="form-paper-label">Sex:</label>
                        <select id="childSex[0]" class="form-control" name="child_sex[0]" value="{{ key_exists('child', $applicant->details) ? $applicant->details['child'][0]['sex']['value'] : '' }}">
                            <option value="Male">Male</option>
                            <option value="Male">Female</option>
                        </select>
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="childAge[0]" class="form-paper-label">Age:</label>
                        <input id="childAge[0]" class="form-control" name="child_age[0]" type="text" value="{{ key_exists('child', $applicant->details) ? $applicant->details['child'][0]['age']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-md-6 form-paper">
                    <div class="form-group">
                        <label for="childAddress[0]" class="form-paper-label">Address:</label>
                        <input id="childAddress[0]" class="form-control" name="child_address[0]" type="text" value="{{ key_exists('child', $applicant->details) ? $applicant->details['child'][0]['address']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-md-6 form-paper">
                    <div class="form-group">
                        <label for="childOccupation[0]" class="form-paper-label">Occupation/Employer:</label>
                        <input id="childOccupation[0]" class="form-control" name="child_occupation[0]" type="text" value="{{ key_exists('child', $applicant->details) ? $applicant->details['child'][0]['occupation']['value'] : '' }}" />
                    </div>
                </div>
            </div>
            <div class="row addContainer" id="addChildContainer">
                <div class="col-12 form-paper">
                    <button class="btn btn-link" type="button" onclick="createNewRow(this, 'child')">Add Child</button>
                </div>
            </div>


            <input type="hidden" id="sibling-index" value="1" />
            <div class="row">
                <div class="col-12 form-paper section-label">Siblings</div>
            </div>

            <div class="row sibling-0">
                <div class="col-12 form-paper">
                    <button type="button" class="close text-muted" data-index="0" onclick="deleteRow(this, 'sibling')" tabindex="-1">&times;</button>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="siblingLastName[0]" class="form-paper-label">Last Name:</label>
                                <input id="siblingLastName[0]" class="form-control" name="sibling_last_name[0]" type="text" value="{{ key_exists('sibling', $applicant->details) ? $applicant->details['sibling'][0]['lastname']['value'] : '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="siblingFirstName[0]" class="form-paper-label">First Name:</label>
                                <input id="siblingFirstName[0]" class="form-control" name="sibling_first_name[0]" type="text" value="{{ key_exists('sibling', $applicant->details) ? $applicant->details['sibling'][0]['firstname']['value'] : '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="siblingMiddleName[0]" class="form-paper-label">Middle Name:</label>
                                <input id="siblingMiddleName[0]" class="form-control" name="sibling_middle_name[0]" type="text" value="{{ key_exists('sibling', $applicant->details) ? $applicant->details['sibling'][0]['middlename']['value'] : '' }}" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="siblingSex[0]" class="form-paper-label">Sex:</label>
                        <select id="siblingSex[0]" class="form-control" name="sibling_sex[0]" type="text" value="{{ key_exists('sibling', $applicant->details) ? $applicant->details['sibling'][0]['sex']['value'] : '' }}">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="siblingAge[0]" class="form-paper-label">Age:</label>
                        <input id="siblingAge[0]" class="form-control" name="sibling_age[0]" type="text" value="{{ key_exists('sibling', $applicant->details) ? $applicant->details['sibling'][0]['age']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="siblingAddress[0]" class="form-paper-label">Address:</label>
                        <input id="siblingAddress[0]" class="form-control" name="sibling_address[0]" type="text" value="{{ key_exists('sibling', $applicant->details) ? $applicant->details['sibling'][0]['address']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="siblingOccupation[0]" class="form-paper-label">Occupation/Employer:</label>
                        <input id="siblingOccupation[0]" class="form-control" name="sibling_occupation[0]" type="text" value="{{ key_exists('sibling', $applicant->details) ? $applicant->details['sibling'][0]['occupation']['value'] : '' }}" />
                    </div>
                </div>
            </div>
            <div class="row addContainer" id="addSiblingContainer">
                <div class="col-12 form-paper">
                    <button class="btn btn-link" type="button" onclick="createNewRow(this, 'sibling')">Add Sibling</button>
                </div>
            </div>

            <div class="row">
                <div class="col-12 form-paper section-title">References</div>
            </div>
            @for ($i = 0; $i < 4; $i++)
            <div class="row reference-{{ $i }}">
                <div class="col-12 form-paper">
                    <div class="form-group">
                        <label for="referenceName[{{ $i }}]" class="form-paper-label">Name:</label>
                        <input id="referenceName[{{ $i }}]" class="form-control" name="reference_name[{{ $i }}]" type="text" value="{{ key_exists('reference', $applicant->details) ? $applicant->details['reference'][$i]['name']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-md-4 col-6 form-paper">
                    <div class="form-group">
                        <label for="referenceOccupation[{{ $i }}]" class="form-paper-label">Occupation:</label>
                        <input id="referenceOccupation[{{ $i }}]" class="form-control" name="reference_occupation[{{ $i }}]" type="text" value="{{ key_exists('reference', $applicant->details) ? $applicant->details['reference'][$i]['occupation']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-md-4 col-6 form-paper">
                    <div class="form-group">
                        <label for="referenceAddress[{{ $i }}]" class="form-paper-label">Address:</label>
                        <input id="referenceAddress[{{ $i }}]" class="form-control" name="reference_address[{{ $i }}]" type="text" value="{{ key_exists('reference', $applicant->details) ? $applicant->details['reference'][$i]['address']['value'] : '' }}" />
                    </div>
                </div>
                <div class="col-md-4 form-paper">
                    <div class="form-group">
                        <label for="referenceContact[{{ $i }}]" class="form-paper-label">Contact Details:</label>
                        <input id="referenceContact[{{ $i }}]" class="form-control" name="reference_contact[{{ $i }}]" type="text" value="{{ key_exists('reference', $applicant->details) ? $applicant->details['reference'][$i]['contact']['value'] : '' }}" />
                    </div>
                </div>
            </div>
            @endfor
            <div class="row" id="addReferenceContainer">
                <div class="col-12 form-paper">
                    <button class="btn btn-link" type="button" onclick="addReference()">Add Reference</button>
                </div>
            </div>

            <input type="hidden" name="additional_info_index" id="additional-info-index" value="5" />
            <div class="row">
                <div class="col-12 form-paper section-title">
                    Additional Information
                </div>
                <div class="col-12 form-paper">
                    <div class="row">
                        <div class="col-1">
                            <div class="form-group">
                                <input id="additionalInformation[0]" type="checkbox" name="additional_information[0]" />
                            </div>
                        </div>
                        <div class="col-11">
                            <div class="form-group">
                                <label for="additionalInformation[0]" class="">Have you ever been found guilty or been penalized for any offense or violation involving moral turpitude or carrying the penalty of disqualification to hold public office?</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-1">
                            <div class="form-group">
                                <input id="additionalInformation[1]" type="checkbox" name="additional_information[1]" />
                            </div>
                        </div>
                        <div class="col-11">
                            <div class="form-group">
                                <label for="additionalInformation[1]" class="">Have you been suspended, discharged, or forced to resign from any of your previous positions? If yes, provide details.</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-1">
                            <div class="form-group">
                                <input id="additionalInformation[2]" type="checkbox" name="additional_information[2]" />
                            </div>
                        </div>
                        <div class="col-11">
                            <div class="form-group">
                                <label for="additionalInformation[2]" class="">Are you willing to accept project employment?</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-1">
                            <div class="form-group">
                                <input id="additionalInformation[3]" type="checkbox" name="additional_information[3]" />
                            </div>
                        </div>
                        <div class="col-11">
                            <div class="form-group">
                                <label for="additionalInformation[3]" class="">Have you taken the CJI pre-employment test? If yes, please provide details.</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-1">
                            <div class="form-group">
                                <input id="additionalInformation[4]" type="checkbox" name="additional_information[4]" />
                            </div>
                        </div>
                        <div class="col-11">
                            <div class="form-group">
                                <label for="additionalInformation[4]" class="">Do you have disablity or health condition that would affect your ability to work?</label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-paper">
                    <div class="form-group">
                        <label for="tin" class="form-paper-label">Tax Identification No.</label>
                        <input id="tin" type="text" class="form-control" name="tinnumber" value="{{ key_exists('tin', $applicant->deductibles) ? $applicant->deductibles['tin'] : '' }}" />
                    </div>
                </div>
                <div class="col-md-6 form-paper">
                    <div class="form-group">
                        <label for="ssn" class="form-paper-label">Social Security No.</label>
                        <input id="ssn" type="text" class="form-control" name="ssnumber" value="{{ key_exists('sss', $applicant->deductibles) ? $applicant->deductibles['sss'] : '' }}" />
                    </div>
                </div>
                <div class="col-md-6 form-paper">
                    <div class="form-group">
                        <label for="philhealth" class="form-paper-label">PhilHealth Identification No.</label>
                        <input id="philhealth" type="text" class="form-control" name="philhealthnumber" value="{{ key_exists('philhealth', $applicant->deductibles) ? $applicant->deductibles['philhealth'] : '' }}" />
                    </div>
                </div>
                <div class="col-md-6 form-paper">
                    <div class="form-group">
                        <label for="pagibig" class="form-paper-label">PAGIBIG No.</label>
                        <input id="pagibig" type="text" class="form-control" name="pagibignumber" value="{{ key_exists('pagibig', $applicant->deductibles) ? $applicant->deductibles['pagibig'] : '' }}" />
                    </div>
                </div>
            </div>
        </div>

            <?php

            // To ensure empty Dependent fields appear,
            // iterate at least one row even if $details['dependent'] is empty

            // $limit = 1; // Store the number of dependents or use 1 if there are none.
            // If there is/are dependent/s use its size instead of the default 1.
            // if (key_exists('dependent', $applicant->details) && is_array($applicant->details['dependent']) && sizeof($applicant->details['dependent']) > 0) {
            //     $limit = sizeof($applicant->details['dependent']);
            // }

            ?>

            {{-- <input type="hidden" id="currentIndex" value="{{ $limit }}" /> --}}

            {{-- Use the $limit variable for looping --}}
            {{-- @for ($i = 0; $i < $limit; $i++) --}}

            {{-- <div class="row dependent-{{$i}}">

                <div class="col-md-8 form-paper border-left">

                    @if ($i == 0) --}}
                    {{-- Show section label and Pluralize --}}
                    {{-- <div class="section-label">Dependent{{ $limit > 1 ? 's' : '' }}</div>
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

            @endfor --}}

            {{-- <div class="form-group" id="addDependentContainer"><button class="btn btn-link" type="button" onclick="addDependent()">Add Dependent</button></div> --}}


            {{-- <div class="form-group"><h5>Employment Information</h5></div>

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
            </div> --}}

            {{-- <div class="row">
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
            </div> --}}
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
                        <a class="btn btn-light" href="{{ action('ApplicantController@index') }}">Back to List</a>
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
                @if ($applicant->id != 0)
                {{-- <form id="imageForm" action="{{ action('applicantController@updateImage', $applicant->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="selectedFilename" name="selected_filename" />
                    <input type="hidden" id="selectedLocation" name="selected_location" />
                    <div class="d-inline-block">
                        <input type="file" class="form-control-file" accept="image/*" name="picture_file" />
                    </div>
                    <div class="d-inline-sm-block float-sm-right">
                        <input type="reset" class="btn btn-light btn-sm" value="Reset" />
                    </div>
                </form> --}}
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
<script src="{{ asset('js/applicationFormPage.js') }}"></script>
@stop
