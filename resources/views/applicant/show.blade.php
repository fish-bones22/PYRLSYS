@extends('layout.master')

@section('title')
{{ $applicant->fullName }}
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
                <div class="col-12 form-paper">
                    <div class="current-image mx-auto m-2">
                        {{-- <input form="applicantForm" type="file" class="form-paper-display-file btn-change-image" accept="image/*" name="new_image_file" /> --}}
                        <img src="{{ $applicant->currentPicture == null ? asset('img/anom.png') : asset('storage/'.$applicant->currentPicture['location'].$applicant->currentPicture['filename']) }}" class="img-fluid" />
                    </div>
                </div>
            </div>

           <div class="row">
                <div class="col-12 text-center form-paper">
                    <div class="display-4">{{ $applicant->fullName }}</div>
                </div>
           </div>

           <div class="row">
                <div class="col-12 form-paper section-title">Basic Information</div>
            </div>

            <div class="row">
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Position Applied for:</label>
                        <div class="form-paper-display">{{ key_exists('position', $applicant->details) ? $applicant->details['position']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="expectedsalary" class="form-paper-label">Expected salary:</label>
                        <div class="form-paper-display">{{ key_exists('expectedsalary', $applicant->details) ? $applicant->details['expectedsalary']['value'] : '' }}</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Respondent to:</label>
                        <div class="form-paper-display">
                            {{ $applicant->details['respondentto']['value'] }}
                        </div>
                        @if (key_exists('respondenttoothers', $applicant->details))
                        <div class="col">
                            <div class="form-paper-subdisplay">
                                {{ $applicant->details['respondenttoothers']['value'] }}
                            </div>
                        </div>
                        @endif
                        @if (key_exists('referralname', $applicant->details) && key_exists('referralposition', $applicant->details))
                        <div class="col form-paper">
                            <div class="form-paper-display">
                                {{ $applicant->details['referralname']['value'] }}
                            </div>
                            <div class="form-paper-subdisplay">
                                {{ $applicant->details['referralposition']['value'] }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 form-paper section-title">Personal Information</div>
            </div>
            <div class="row">
                <div class="col-12 form-paper">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="lastname" class="form-paper-label">Last name:</label>
                                <div class="form-paper-display"> {{ $applicant->lastName }}</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="firstname" class="form-paper-label">First name:</label>
                                <div class="form-paper-display"> {{ $applicant->firstName }}</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="middlename" class="form-paper-label">Middle name:</label>
                                <div class="form-paper-display"> {{ $applicant->middleName }}</div>
                            </div>
                        </div>
                        {{-- <div class="col-3">
                            <div class="form-group">
                                <label for="maidenName" class="form-paper-label">Maiden Name (if married):</label>
                                <div class="form-paper-display">{{ $applicant->middleName }}</div>
                            </div>
                        </div> --}}
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Sex:</label>
                        <div class="form-paper-display">{{ $applicant->sex == 'm' ? 'Male' : 'Female' }}</div>
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Civil Status:</label>
                        <div class="form-paper-display">{{ key_exists('civilstatus', $applicant->details) ? $applicant->details['civilstatus']['value'] : '' }}</div>
                    </div>
                </div>

                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label for="dateOfBirth" class="form-paper-label">Date of Birth:</label>
                        <div class="form-paper-display">{{ key_exists('dateofbirth', $applicant->details) ? $applicant->details['dateofbirth']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-3 form-paper">
                    <div class="form-group">
                        <label for="age" class="form-paper-label">Age:</label>
                        <div class="form-paper-display">{{ key_exists('age', $applicant->details) ? $applicant->details['age']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-5 form-paper">
                    <div class="form-group">
                        <label for="placeOfBirth" class="form-paper-label">Place of Birth:</label>
                        <div class="form-paper-display">{{ key_exists('placeofbirth', $applicant->details) ? $applicant->details['placeofbirth']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="citizenship" class="form-paper-label">Citizenship:</label>
                        <div class="form-paper-display">{{ key_exists('citizenship', $applicant->details) ? $applicant->details['citizenship']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="religion" class="form-paper-label">Religion:</label>
                        <div class="form-paper-display">{{ key_exists('religion', $applicant->details) ? $applicant->details['religion']['value'] : '' }}</div>
                    </div>
                </div>

                <div class="col-8 form-paper">
                    <div class="form-group">
                        <label for="presentAddress" class="form-paper-label">Present Address:</label>
                        <div class="form-paper-display">{{ key_exists('presentaddress', $applicant->details) ? $applicant->details['presentaddress']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label for="presentAddressContact" class="form-paper-label">Tel/Mobile No.:</label>
                        <div class="form-paper-display">{{ key_exists('presentaddresscontact', $applicant->details) ? $applicant->details['presentaddresscontact']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-8 form-paper">
                    <div class="form-group">
                        <label for="permanentAddress" class="form-paper-label">Permanent Address:</label>
                        <div class="form-paper-display">{{ key_exists('permanentaddress', $applicant->details) ? $applicant->details['permanentaddress']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label for="permanentAddressContact" class="form-paper-label">Tel/Mobile No.:</label>
                        <div class="form-paper-display">{{ key_exists('permanentaddresscontact', $applicant->details) ? $applicant->details['permanentaddresscontact']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-8 form-paper">
                    <div class="form-group">
                        <label for="emailAddress" class="form-paper-label">Email Address:</label>
                        <div class="form-paper-display">{{ key_exists('emailaddress', $applicant->details) ? $applicant->details['emailaddress']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label for="contactNumber" class="form-paper-label">Personal Contact No.:</label>
                        <div class="form-paper-display">{{ key_exists('contactnumber', $applicant->details) ? $applicant->details['contactnumber']['value'] : '' }}</div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-12 form-paper section-title">Educational Attainment</div>
            </div>

            @if (key_exists('education', $applicant->details))
            @for ($i = 0; $i < sizeof($applicant->details['education']); $i++)

            <div class="row">
                <div class="col-12 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Level:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['education'][$i]['level']) ? $applicant->details['education'][$i]['level']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Name of School:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['education'][$i]['nameofschool']) ? $applicant->details['education'][$i]['nameofschool']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="course[0]" class="form-paper-label">Course:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['education'][$i]['course']) ? $applicant->details['education'][$i]['course']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-5 form-paper border-bottom">
                    <div class="form-group">
                        <label class="form-paper-label">Year Graduated:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['education'][$i]['yeargraduated']) ? $applicant->details['education'][$i]['yeargraduated']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-7 form-paper border-bottom">
                    <div class="form-group">
                        <label class="form-paper-label">Honors/Awards:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['education'][$i]['recognition']) && key_exists('recognition', $applicant->details['education'][$i]) ? $applicant->details['education'][$i]['recognition']['value'] : ''}}</div>
                    </div>
                </div>
            </div>

            @endfor
            @else
            <div class="row">
                <div class="col-12 form-paper">
                    <div class="form-paper-subdisplay">No Educational Attainment</div>
                </div>
            </div>
            @endif


            <div class="row">
                <div class="col-12 form-paper section-title">Examinations</div>
            </div>

            @if (key_exists('examination', $applicant->details))
            @for ($i = 0; $i < sizeof($applicant->details['examination']); $i++)

            <div class="row">
                <div class="col-7 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Title of Examination:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['examination'][$i]['titleofexam']) ? $applicant->details['examination'][$i]['titleofexam']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-5 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Date of Examination:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['examination'][$i]['dateofexam']) ? $applicant->details['examination'][$i]['dateofexam']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-7 form-paper border-bottom">
                    <div class="form-group">
                        <label class="form-paper-label">Place of Examination:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['examination'][$i]['placeofexam']) ? $applicant->details['examination'][$i]['placeofexam']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-5 form-paper border-bottom">
                    <div class="form-group">
                        <label class="form-paper-label">Rating:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['examination'][$i]['rating']) ? $applicant->details['examination'][$i]['rating']['value'] : '' }}</div>
                    </div>
                </div>
            </div>

            @endfor
            @else
            <div class="row">
                <div class="col-12 form-paper">
                    <div class="form-paper-subdisplay">No Examinations Taken</div>
                </div>
            </div>
            @endif


            <div class="row">
                <div class="col-12 form-paper section-title">Employment History</div>
            </div>

            @if (key_exists('employmentrecord', $applicant->details))
            @for ($i = 0; $i < sizeof($applicant->details['employmentrecord']); $i++)

            <div class="row employment-record-0">
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">From:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['employmentrecord'][$i]['datefrom']) ? $applicant->details['employmentrecord'][$i]['datefrom']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">To:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['employmentrecord'][$i]['dateto']) ? $applicant->details['employmentrecord'][$i]['dateto']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-7 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Position:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['employmentrecord'][$i]['position']) ? $applicant->details['employmentrecord'][$i]['position']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-5 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Status:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['employmentrecord'][$i]['status']) ? $applicant->details['employmentrecord'][$i]['status']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-12 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Employer/Location:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['employmentrecord'][$i]['employer']) ? $applicant->details['employmentrecord'][$i]['employer']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Gross Monthly Salary:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['employmentrecord'][$i]['salary']) ? $applicant->details['employmentrecord'][$i]['salary']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Reason for Leaving:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['employmentrecord'][$i]['reasonforleaving']) ? $applicant->details['employmentrecord'][$i]['reasonforleaving']['value'] : '' }}</div>
                    </div>
                </div>
            </div>
            @endfor
            @else
            <div class="row">
                <div class="col-12 form-paper">
                    <div class="form-paper-subdisplay">No Employment History</div>
                </div>
            </div>
            @endif


            <div class="row">
                <div class="col-12 form-paper section-title">Trainings and Seminars</div>
            </div>

            @if (key_exists('training', $applicant->details))
            @for ($i = 0; $i < sizeof($applicant->details['training']); $i++)

            <div class="row">
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">From:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['training'][$i]['datefrom']) ? $applicant->details['training'][$i]['datefrom']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label for="traingingDateTo[0]" class="form-paper-label">To:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['training'][$i]['dateto']) ? $applicant->details['training'][$i]['dateto']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-12 form-paper">
                    <div class="form-group">
                        <label for="traingingTitle[0]" class="form-paper-label">Title/Course:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['training'][$i]['title']) ? $applicant->details['training'][$i]['title']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-8 form-paper">
                    <div class="form-group">
                        <label for="trainingVenue[0]" class="form-paper-label">Venue:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['training'][$i]['venue']) ? $applicant->details['training'][$i]['venue']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label for="trainingNumberOfHours[0]" class="form-paper-label">Hours:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['training'][$i]['hours']) ? $applicant->details['training'][$i]['hours']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-12 form-paper border-bottom">
                    <div class="form-group">
                        <label for="trainingOrganizer[0]" class="form-paper-label">Organizer/Sponsor:</label>
                        <div class="form-paper-display">{{ isset($applicant->details['training'][$i]['organizer']) ? $applicant->details['training'][$i]['organizer']['value'] : '' }}</div>
                    </div>
                </div>
            </div>

            @endfor
            @else
            <div class="row">
                <div class="col-12 form-paper">
                    <div class="form-paper-subdisplay">No Trainings or Seminars Taken</div>
                </div>
            </div>
            @endif


            <div class="row">
                <div class="col-12 form-paper section-title">Family Background</div>
            </div>


            @if (key_exists('spouse', $applicant->details))

            <div class="row">
                <div class="col-12 form-paper section-label">Spouse (Maiden) Name</div>
            </div>

            @for ($i = 0; $i < sizeof($applicant->details['spouse']); $i++)

            <div class="row">
                <div class="col-12 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Name:</label>
                        <?php
                        $first = isset($applicant->details['spouse'][0]['firstname']) ? $applicant->details['spouse'][0]['firstname']['value'] : '';
                        $middle = isset($applicant->details['spouse'][0]['middlename']) ? $applicant->details['spouse'][0]['middlename']['value'] : '';
                        $last = isset($applicant->details['spouse'][0]['lastname']) ? $applicant->details['spouse'][0]['lastname']['value'] : '';
                        $fullname = $last.', '.$first.' '.$middle;
                        ?>
                        <div class="form-paper-display">{{ key_exists('spouse', $applicant->details) ? $fullname : '' }}</div>
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label for="spouseAge" class="form-paper-label">Age:</label>
                        <div class="form-paper-display">{{ $applicant->details['spouse'][$i]['age']['value'] }}</div>
                    </div>
                </div>
                <div class="col-8 form-paper">
                    <div class="form-group">
                        <label for="spouseAddress" class="form-paper-label">Address:</label>
                        <div class="form-paper-display">{{ $applicant->details['spouse'][$i]['address']['value'] }}</div>
                    </div>
                </div>
                <div class="col-5 form-paper">
                    <div class="form-group">
                        <label for="dateOfMarriage" class="form-paper-label">Date of Marriage:</label>
                        <div class="form-paper-display">{{ $applicant->details['spouse'][$i]['dateofmarriage']['value'] }}</div>
                    </div>
                </div>
                <div class="col-7 form-paper">
                    <div class="form-group">
                        <label for="placeOfMarriage" class="form-paper-label">Place of Marriage:</label>
                        <div class="form-paper-display">{{ $applicant->details['spouse'][$i]['placeofmarriage']['value'] }}</div>
                    </div>
                </div>
                <div class="col-5 form-paper">
                    <div class="form-group">
                        <label for="occupationOfSpouse" class="form-paper-label">Occupation of Spouse:</label>
                        <div class="form-paper-display">{{ $applicant->details['spouse'][$i]['occupation']['value'] }}</div>
                    </div>
                </div>
                <div class="col-7 form-paper">
                    <div class="form-group">
                        <label for="employerOfSpouse" class="form-paper-label">Employer of Spouse:</label>
                        <div class="form-paper-display">{{ $applicant->details['spouse'][$i]['employer']['value'] }}</div>
                    </div>
                </div>
            </div>
            @endfor
            {{-- @else
            <div class="row">
                <div class="col-12 form-paper">
                    <div class="form-paper-subdisplay">No Spouse</div>
                </div>
            </div> --}}
            @endif


            <div class="row">
                <div class="col-12 form-paper section-label">Mother</div>
            </div>

            @if (key_exists('mother', $applicant->details))
            <div class="row">
                <div class="col-md-10 form-paper">
                    <div class="form-group">
                        <label for="motherLastName" class="form-paper-label">Maiden Name:</label>
                        <?php
                        $first = isset($applicant->details['mother']['firstname']) ? $applicant->details['mother']['firstname']['value'] : '';
                        $middle = isset($applicant->details['mother']['middlename']) ? $applicant->details['mother']['middlename']['value'] : '';
                        $last = isset($applicant->details['mother']['lastname']) ? $applicant->details['mother']['lastname']['value'] : '';
                        $fullname = $last.', '.$first.' '.$middle;
                        ?>
                        <div class="form-paper-display">{{ key_exists('mother', $applicant->details) ? $fullname : '' }}</div>
                    </div>
                </div>
                <div class="col-md-2 form-paper">
                    <div class="form-group">
                        <label for="motherAge" class="form-paper-label">Age</label>
                        <div class="form-paper-display">{{ key_exists('mother', $applicant->details) && isset($applicant->details['mother']['age']) ? $applicant->details['mother']['age']['value'] : '' }}</div>
                    </div>
                </div>
            </div>
            @else
            <div class="row">
                <div class="col-12 form-paper">
                    <div class="form-paper-subdisplay">No Information about Mother</div>
                </div>
            </div>
            @endif


            <div class="row">
                <div class="col-12 form-paper section-label">Father</div>
            </div>

            @if (key_exists('father', $applicant->details))
            <div class="row">
                <div class="col-md-10 form-paper">
                    <div class="form-group">
                        <label for="motherLastName" class="form-paper-label">Name:</label>
                        <?php
                        $first = isset($applicant->details['father']['firstname']) ? $applicant->details['father']['firstname']['value'] : '';
                        $middle = isset($applicant->details['father']['middlename']) ? $applicant->details['father']['middlename']['value'] : '';
                        $last = isset($applicant->details['father']['lastname']) ? $applicant->details['father']['lastname']['value'] : '';
                        $fullname = $last.', '.$first.' '.$middle;
                        ?>
                        <div class="form-paper-display">{{ key_exists('father', $applicant->details) ? $fullname : '' }}</div>
                    </div>
                </div>
                <div class="col-md-2 form-paper">
                    <div class="form-group">
                        <label for="motherAge" class="form-paper-label">Age</label>
                        <div class="form-paper-display">{{ key_exists('father', $applicant->details) && isset($applicant->details['father']['age']) ? $applicant->details['father']['age']['value'] : '' }}</div>
                    </div>
                </div>
            </div>
            @else
            <div class="row">
                <div class="col-12 form-paper">
                    <div class="form-paper-subdisplay">No Information about Father</div>
                </div>
            </div>
            @endif


            @if (key_exists('child', $applicant->details))

            <div class="row">
                <div class="col-12 form-paper section-label">Children</div>
            </div>

            @for ($i = 0; $i < sizeof($applicant->details['child']); $i++)

            <div class="row">
                <div class="col-12 form-paper">
                    <div class="form-group">
                        <?php

                        $first = isset($applicant->details['child'][$i]['firstname']) ? $applicant->details['child'][$i]['firstname']['value'] : '';
                        $middle = isset($applicant->details['child'][$i]['middlename']) ? $applicant->details['child'][$i]['middlename']['value'] : '';
                        $last = isset($applicant->details['child'][$i]['lastname']) ? $applicant->details['child'][$i]['lastname']['value'] : '';
                        $fullname = $last.', '.$first.' '.$middle;

                        ?>
                        <label class="form-paper-label">Name:</label>
                        <div class="form-paper-display">{{ key_exists('child', $applicant->details) ? $fullname : '' }}</div>
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Sex:</label>
                        <div class="form-paper-display">{{ key_exists('child', $applicant->details) ? $applicant->details['child'][$i]['sex']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-6 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Age:</label>
                        <div class="form-paper-display">{{ key_exists('child', $applicant->details) ? $applicant->details['child'][$i]['age']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-md-6 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Address:</label>
                        <div class="form-paper-display">{{ key_exists('child', $applicant->details) ? $applicant->details['child'][$i]['address']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-md-6 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Occupation/Employer:</label>
                        <div class="form-paper-display">{{ key_exists('child', $applicant->details) ? $applicant->details['child'][$i]['occupation']['value'] : '' }}</div>
                    </div>
                </div>
            </div>

            @endfor
            {{-- @else
            <div class="row">
                <div class="col-12 form-paper">
                    <div class="form-paper-subdisplay">No Children</div>
                </div>
            </div> --}}
            @endif



            @if (key_exists('sibling', $applicant->details))

            <div class="row">
                <div class="col-12 form-paper section-label">Siblings</div>
            </div>

            @for ($i = 0; $i < sizeof($applicant->details['sibling']); $i++)

            <div class="row">
                <div class="col-8 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Name:</label>
                        <div class="form-paper-display">{{ key_exists('sibling', $applicant->details) ? $applicant->details['sibling'][$i]['lastname']['value'].', '. $applicant->details['sibling'][$i]['firstname']['value'].' '. $applicant->details['sibling'][$i]['middlename']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-2 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Sex:</label>
                        <div class="form-paper-display">{{ key_exists('sex', $applicant->details['sibling'][$i]) ? $applicant->details['sibling'][$i]['sex']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-2 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Age:</label>
                        <div class="form-paper-display">{{ key_exists('age', $applicant->details['sibling'][$i]) ? $applicant->details['sibling'][$i]['age']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-md-6 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Address:</label>
                        <div class="form-paper-display">{{ key_exists('address', $applicant->details['sibling'][$i]) ? $applicant->details['sibling'][$i]['address']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-md-6 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Occupation/Employer:</label>
                        <div class="form-paper-display">{{ key_exists('occupation', $applicant->details['sibling'][$i]) ? $applicant->details['sibling'][$i]['occupation']['value'] : '' }}</div>
                    </div>
                </div>
            </div>

            @endfor
            {{-- @else
            <div class="row">
                <div class="col-12 form-paper">
                    <div class="form-paper-subdisplay">No Siblings</div>
                </div>
            </div> --}}
            @endif


            <div class="row">
                <div class="col-12 form-paper section-title">References</div>
            </div>

            @if (key_exists('reference', $applicant->details))
            @for ($i = 0; $i < sizeof($applicant->details['reference']); $i++)

            <div class="row">
                <div class="col-12 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Name:</label>
                        <div class="form-paper-display">{{ key_exists('reference', $applicant->details) ? $applicant->details['reference'][$i]['name']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-6 form-paper">
                    <div class="form-group">
                        <label for="referenceOccupation[{{ $i }}]" class="form-paper-label">Occupation:</label>
                        <div class="form-paper-display">{{ key_exists('reference', $applicant->details) ? $applicant->details['reference'][$i]['occupation']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-6 form-paper">
                    <div class="form-group">
                        <label for="referenceAddress[{{ $i }}]" class="form-paper-label">Address:</label>
                        <div class="form-paper-display">{{ key_exists('reference', $applicant->details) ? $applicant->details['reference'][$i]['address']['value'] : '' }}</div>
                    </div>
                </div>
                <div class="col-md-4 form-paper">
                    <div class="form-group">
                        <label for="referenceContact[{{ $i }}]" class="form-paper-label">Contact Details:</label>
                        <div class="form-paper-display">{{ key_exists('reference', $applicant->details) ? $applicant->details['reference'][$i]['contact']['value'] : '' }}</div>
                    </div>
                </div>
            </div>
            @endfor
            @else
            <div class="row">
                <div class="col-12 form-paper">
                    <div class="form-paper-subdisplay">No References</div>
                </div>
            </div>
            @endif


            <input type="hidden" name="additional_info_index" id="additional-info-index" value="5" />
            <div class="row">
                <div class="col-12 form-paper section-title">
                    Additional Information
                </div>
                <div class="col-12 form-paper">

                    @if (key_exists('additionalinfo', $applicant->details))
                    @for ($i = 0; $i < sizeof($applicant->details['additionalinfo']); $i++)
                    <div class="row">
                        <div class="col-1">
                            <div class="form-group">
                                <div class="form-paper-display">{{ $applicant->details['additionalinfo'][$i]['value'] }}</div>
                            </div>
                        </div>
                        <div class="col-11">
                            <div class="form-group">
                                <label class="">{{ $applicant->details['additionalinfo'][$i]['displayName'] }}</label>
                                @if ($applicant->details['additionalinfo'][$i]['detail'] != null)
                                <div>
                                    <label class="form-paper-subdisplay">{{ $applicant->details['additionalinfo'][$i]['detail'] }}</label>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endfor
                    @else
                    <div class="row">
                        <div class="col-12">
                            No Additional Information
                        </div>
                    </div>
                    @endif

                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-paper">
                    <div class="form-group">
                        <label for="tin" class="form-paper-label">Tax Identification No.</label>
                        <div class="form-paper-display">{{ key_exists('tin', $applicant->deductibles) ? $applicant->deductibles['tin'] : '' }}</div>
                    </div>
                </div>
                <div class="col-md-6 form-paper">
                    <div class="form-group">
                        <label for="ssn" class="form-paper-label">Social Security No.</label>
                        <div class="form-paper-display">{{ key_exists('sss', $applicant->deductibles) ? $applicant->deductibles['sss'] : '' }}</div>
                    </div>
                </div>
                <div class="col-md-6 form-paper">
                    <div class="form-group">
                        <label for="philhealth" class="form-paper-label">PhilHealth Identification No.</label>
                        <div class="form-paper-display">{{ key_exists('philhealth', $applicant->deductibles) ? $applicant->deductibles['philhealth'] : '' }}</div>
                    </div>
                </div>
                <div class="col-md-6 form-paper">
                    <div class="form-group">
                        <label for="pagibig" class="form-paper-label">PAGIBIG No.</label>
                        <div class="form-paper-display">{{ key_exists('pagibig', $applicant->deductibles) ? $applicant->deductibles['pagibig'] : '' }}</div>
                    </div>
                </div>
            </div>

            <div class="m-4">&nbsp;</div>
            <div class="fixed-bottom btn-container m-4">
                <div class="float-right">
                    <div class="btn-group">
                        <a class="btn btn-light" href="{{ action('ApplicantController@index') }}">Back to List</a>
                        <input type="submit" class="btn btn-secondary" value="Print"/>
                        <a href="{{ action('ApplicantController@process', $applicant->id) }}" class="btn btn-primary" value="Process">Process</a>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

@stop

@section('script')
<script src="{{ asset('js/imageSelection.js') }}"></script>
<script src="{{ asset('js/applicationFormPage.js') }}"></script>
@stop
