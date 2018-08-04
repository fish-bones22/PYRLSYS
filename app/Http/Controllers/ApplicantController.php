<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Intervention\Image\ImageManagerStatic as Image;

use App\Contracts\ICategoryService;
use App\Contracts\IEmployeeService;
use App\Models\Employee;
use App\Models\EmployeePicture;
use App\Entities\EmployeeEntity;

class ApplicantController extends Controller
{
    protected $employeeService;
    protected $categoryService;

    public function __construct(IEmployeeService $employeeService, ICategoryService $categoryService) {

        $this->employeeService = $employeeService;
        $this->categoryService = $categoryService;

    }

    public function index() {

        $applicants = $this->employeeService->getAllEmployees();

        return view('applicant.index', compact('applicants'));

    }


    public function new() {

        $applicant = new EmployeeEntity();
        return view('applicant.new', compact('applicant'));
    }

    public function show($id = 0) {

        $applicant= $this->employeeService->getEmployeeById($id);
        return view('applicant.show', ['applicant' => $applicant]);

    }


    public function update(Request $request, $id) {

        $req = $request->all();

        $applicant = new EmployeeEntity;
        $applicant->id = $id;
        $applicant->firstName = $req['first_name'];
        $applicant->lastName = $req['last_name'];
        $applicant->middleName = $req['middle_name'];
        $applicant->employeeId = null;
        $applicant->sex = $req['sex'];

        $applicant->details = $this->detailsToEntity($req);
        // $applicant->employmentDetails = $this->employmentDetailsToEntity($req);
        $applicant->deductibles = $this->deductiblesToEntity($req);

        if ($id != 0) {
            $result = $this->employeeService->updateEmployee($employee);
            if (!$result['result']) {
                return redirect()->action('ApplicantController@show', $id)->with('error', $result['message']);
            }
        }
        else {

            $result = $this->employeeService->addEmployee($applicant);
            if (!$result['result']) {
                return redirect()->action('ApplicantController@show', $id)->with('error', $result['message']);
            }
            $id = $result['result'];
            // If an image is selected
            if ($request->file('new_image_file')) {

                // Save image file to storage
                $image = $request->file('new_image_file');
                $location = 'profilepictures/';
                $filename = time().$id.'.'.$image->getClientOriginalExtension();
                $this->saveImageToStorage($image, $location, $filename);
                // Save data to DB
                $this->employeeService->addEmployeeImage($id, $location, $filename);
            }
        }


        return redirect()->action('ApplicantController@new', $id)->with('success', '');

    }

    public function destroy($id) {

        $result = $this->employeeService->removeEmployee($id);

        if (!$result['result'])
            return redirect()->action('ApplicantController@index')->with('error', $result['message']);

        return redirect()->action('ApplicantController@index')->with('success', '');

    }


    public function updateImage(Request $request, $id) {

        if (!$request->file('picture_file')
        && !$request->get('selected_filename')
        && !$request->get('selected_location')) {
            return redirect()->action('ApplicantController@show', $id)->with('error', 'Image file is not valid');
        }

        // Set current picture to not current
        $this->employeeService->unsetCurrentEmployeeImage($id);

        // If new file is uploaded
        if ($request->file('picture_file')) {
            // Get HTTP file
            $image = $request->file('picture_file');
            // Path and file name
            $location = 'profilepictures/';
            $filename = time().$id.'.'.$image->getClientOriginalExtension();

            $this->saveImageToStorage($image, $location, $filename);
            // Save data to DB
            $this->employeeService->addEmployeeImage($id, $location, $filename);

        }
        // If previous image is selected
        else if ($request->get('selected_filename') && $request->get('selected_location')) {

            $location = $request->get('selected_location');
            $filename = $request->get('selected_filename');
            // Update DB
            $this->employeeService->setEmployeeImage($id, $location, $filename);
        }

        return redirect()->action('ApplicantController@show', $id)
        ->with('success', 'Image successfully changed');
    }


    public function deleteImage(Request $request, $id) {

        $location = $request->get('location');
        $filename = $request->get('filename');

        $this->removeImageFromStorage($location.$filename);

        $this->employeeService->removeEmployeeImage($id, $location, $filename);

        return redirect()->action('ApplicantController@show', $id);
    }


    private function saveImageToStorage($file, $location, $filename) {
        // Resize amd crop image to square
        $resizedImg = Image::make($file);
        $resizedImg = $this->resizeImage($resizedImg);
        // Store to file to storage
        Storage::put('public/'.$location.$filename, (string) $resizedImg->encode());
    }

    private function removeImageFromStorage($path) {
        Storage::delete('public/'.$path);
    }

    private function resizeImage($image) {

        $size = 300;
        $newWidth = $image->width();
        $newHeight = $image->height();

        // if image is portrait
        if ($newHeight > $newWidth) {
            $newHeight = ($newHeight/$newWidth) * $size;
            $newWidth = $size;
        } else { // if landscape
            $newWidth = ($newWidth/$newHeight) * $size;
            $newHeight = $size;
        }

        $image->resize($newWidth, $newHeight)->crop($size, $size);
        return $image;

    }


    private function detailsToEntity($details) {

        $entity = array();

        // Position
        $entity['position'] = [
            'key' => 'position',
            'value' => $details['position'],
            'displayName' => 'Position'
        ];

        // Expected Salary
        $entity['expectedsalary'] = [
            'key' => 'expectedsalary',
            'value' => $details['expected_salary'],
            'displayName' => 'Expected Salary'
        ];

        // Civil status
        $entity['civilstatus'] = [
            'key' => 'civilstatus',
            'value' => $details['civil_status'],
            'displayName' => 'Civil Status'
        ];

        // Respondent to
        $entity['respondentto'] = [
            'key' => 'respondentto',
            'value' => $details['respondent_to'],
            'displayName' => 'Respondent to'
        ];

        // Respondent to Others
        if (isset($details['respondent_to_others']) && $details['respondent_to_others'] != '') {
            $entity['respondenttoothers'] = [
                'key' => 'respondenttoothers',
                'value' => $details['respondent_to_others'],
                'displayName' => 'Others'
            ];
        }

        if (isset($details['referral_name']) && $details['referral_name'] != '') {
            $entity['referralname'] = [
                'key' => 'referralname',
                'value' => $details['referral_name'],
                'displayName' => 'Referral Name'
            ];
        }

        if (isset($details['referral_position']) && $details['referral_position'] != '') {
            $entity['referralposition'] = [
                'key' => 'referralposition',
                'value' => $details['referral_position'],
                'displayName' => 'Referral Position'
            ];
        }

        // Date of birth
        $entity['dateofbirth'] = [
            'key' => 'dateofbirth',
            'value' => $details['date_of_birth'],
            'displayName' => 'Date of Birth'
        ];

        // Age
        $entity['age'] = [
            'key' => 'age',
            'value' => $details['age'],
            'displayName' => 'Age'
        ];

        // Place of Birth
        $entity['placeofbirth'] = [
            'key' => 'placeofbirth',
            'value' => $details['place_of_birth'],
            'displayName' => 'Place of Birth'
        ];

        // Citizenship
        $entity['citizenship'] = [
            'key' => 'citizenship',
            'value' => $details['citizenship'],
            'displayName' => 'Citizenship'
        ];

        // Religion
        $entity['religion'] = [
            'key' => 'religion',
            'value' => $details['religion'],
            'displayName' => 'Religion'
        ];

        // Present Address
        $entity['presentaddress'] = [
            'key' => 'presentaddress',
            'value' => $details['present_address'],
            'displayName' => 'Present Address'
        ];

        // Present Address Contact
        if (isset($details['present_address_contact']) && $details['present_address_contact'] != '') {
            $entity['presentaddresscontact'] = [
                'key' => 'presentaddresscontact',
                'value' => $details['present_address_contact'],
                'displayName' => 'Present Address Contact'
            ];
        }

        // Permanent Address
        $entity['permanentaddress'] = [
            'key' => 'permanentaddress',
            'value' => $details['permanent_address'],
            'displayName' => 'Permanent Address'
        ];

        // Permanent Address Contact
        if (isset($details['permanent_address_contact']) && $details['permanent_address_contact'] != '')
            $entity['permanentaddresscontact'] = [
                'key' => 'permanentaddresscontact',
                'value' => $details['permanent_address_contact'],
                'displayName' => 'Permanent Address Contact'
            ];

        // Email Address
        if (isset($details['email_address']) && $details['email_address'] != '')
            $entity['emailaddress'] = [
                'key' => 'emailaddress',
                'value' => $details['email_address'],
                'displayName' => 'Email Address'
            ];

        // Contact Number
        $entity['contactnumber'] = [
            'key' => 'contactnumber',
            'value' => $details['contact_number'],
            'displayName' => 'Contact Number'
        ];

        // Educational Attainment
        $entity['education'] = array();
        for ($i = 0; $i < sizeof($details['level']); $i++) {

            $entity['education'][] = [
                'level' => [
                    'key' => 'level',
                    'grouping' => $i,
                    'value' => $details['level'][$i],
                    'displayName' => 'Level'
                ],
                'nameofschool' => [
                    'key' => 'nameofschool',
                    'grouping' => $i,
                    'value' => $details['name_of_school'][$i],
                    'displayName' => 'Name of School'
                ],
                'course' => [
                    'key' => 'course',
                    'grouping' => $i,
                    'value' => $details['course'][$i],
                    'displayName' => 'Course'
                ],
                'yeargraduated' => [
                    'key' => 'yeargraduated',
                    'grouping' => $i,
                    'value' => $details['year_graduated'][$i] != '' ? $details['year_graduated'][$i] : null,
                    'displayName' => 'Year Graduated'
                ],
                'recognition' => [
                    'key' => 'recognition',
                    'grouping' => $i,
                    'value' => $details['recognition'][$i] != '' ? $details['recognition'][$i] : null,
                    'displayName' => 'Honors/Awards'
                ]
            ];
        }

        // Examinations
        $entity['examination'] = array();
        for ($i = 0; $i < sizeof($details['title_of_exam']); $i++) {

            if (sizeof($details['title_of_exam']) <= 1 && $details['title_of_exam'] == '')
                break;

            $entity['examination'][] = [
                'titleofexam' => [
                    'key' => 'titleofexam',
                    'grouping' => $i,
                    'value' => $details['title_of_exam'][$i],
                    'displayName' => 'Title of Exam'
                ],
                'dateofexam' => [
                    'key' => 'dateofexam',
                    'grouping' => $i,
                    'value' => $details['date_of_exam'][$i],
                    'displayName' => 'Date of Exam'
                ],
                'placeofexam' => [
                    'key' => 'placeofexam',
                    'grouping' => $i,
                    'value' => $details['place_of_exam'][$i],
                    'displayName' => 'Place of Exam'
                ],
                'rating' => [
                    'key' => 'rating',
                    'grouping' => $i,
                    'value' => $details['rating'][$i],
                    'displayName' => 'Rating'
                ]
            ];
        }

        // Employment record
        $entity['employmentrecord'] = array();
        for ($i = 0; $i < sizeof($details['employment_record_date_from']); $i++) {

            if (sizeof($details['employment_record_date_from']) <= 1 && $details['employment_record_date_from'] == '')
                break;

            $entity['employmentrecord'][] = [
                'datefrom' => [
                    'key' => 'datefrom',
                    'grouping' => $i,
                    'value' => $details['employment_record_date_from'][$i],
                    'displayName' => 'From'
                ],
                'dateto' => [
                    'key' => 'dateto',
                    'grouping' => $i,
                    'value' => $details['employment_record_date_to'][$i],
                    'displayName' => 'To'
                ],
                'position' => [
                    'key' => 'position',
                    'grouping' => $i,
                    'value' => $details['employment_record_position'][$i],
                    'displayName' => 'Position'
                ],
                'status' => [
                    'key' => 'status',
                    'grouping' => $i,
                    'value' => $details['employment_record_status'][$i],
                    'displayName' => 'Status'
                ],
                'employer' => [
                    'key' => 'employer',
                    'grouping' => $i,
                    'value' => $details['employment_record_employer'][$i],
                    'displayName' => 'Employer/Location'
                ],
                'salary' => [
                    'key' => 'salary',
                    'grouping' => $i,
                    'value' => $details['employment_record_salary'][$i],
                    'displayName' => 'Gross Monthly Salary'
                ],
                'reasonforleaving' => [
                    'key' => 'reasonforleaving',
                    'grouping' => $i,
                    'value' => $details['employment_record_reason_for_leaving'][$i],
                    'displayName' => 'Reason for Leaving'
                ]
            ];
        }

        // Training
        $entity['training'] = array();
        for ($i = 0; $i < sizeof($details['training_date_from']); $i++) {

            if (sizeof($details['training_date_from']) <= 1 && $details['training_date_from'] == '')
                break;

            $entity['training'][] = [
                'datefrom' => [
                    'key' => 'datefrom',
                    'grouping' => $i,
                    'value' => $details['training_date_from'][$i],
                    'displayName' => 'From'
                ],
                'dateto' => [
                    'key' => 'dateto',
                    'grouping' => $i,
                    'value' => $details['training_date_to'][$i],
                    'displayName' => 'To'
                ],
                'title' => [
                    'key' => 'title',
                    'grouping' => $i,
                    'value' => $details['training_title'][$i],
                    'displayName' => 'Title'
                ],
                'venue' => [
                    'key' => 'venue',
                    'grouping' => $i,
                    'value' => $details['training_venue'][$i],
                    'displayName' => 'Venue'
                ],
                'hours' => [
                    'key' => 'hours',
                    'grouping' => $i,
                    'value' => $details['training_hours'][$i],
                    'displayName' => 'Number of Hours'
                ],
                'organizer' => [
                    'key' => 'organizer',
                    'grouping' => $i,
                    'value' => $details['training_organizer'][$i],
                    'displayName' => 'Organizer/Sponsor'
                ]
            ];
        }

        // Spouse
        $entity['spouse'] = array();
        for ($i = 0; $i < sizeof($details['spouse_last_name']); $i++) {

            if (sizeof($details['spouse_last_name']) <= 1 && $details['spouse_last_name'] == '')
                break;

            $entity['spouse'][] = [
                'lastname' => [
                    'key' => 'lastname',
                    'grouping' => $i,
                    'value' => $details['spouse_last_name'][$i],
                    'displayName' => 'Last Name'
                ],
                'firstname' => [
                    'key' => 'firstname',
                    'grouping' => $i,
                    'value' => $details['spouse_first_name'][$i],
                    'displayName' => 'First Name'
                ],
                'middlename' => [
                    'key' => 'middlename',
                    'grouping' => $i,
                    'value' => $details['spouse_middle_name'][$i],
                    'displayName' => 'Middle Name'
                ],
                'age' => [
                    'key' => 'age',
                    'grouping' => $i,
                    'value' => $details['spouse_age'][$i],
                    'displayName' => 'Age'
                ],
                'address' => [
                    'key' => 'address',
                    'grouping' => $i,
                    'value' => $details['spouse_address'][$i],
                    'displayName' => 'Address'
                ],
                'dateofmarriage' => [
                    'key' => 'dateofmarriage',
                    'grouping' => $i,
                    'value' => $details['date_of_marriage'][$i],
                    'displayName' => 'Date of Marriage'
                ],
                'placeofmarriage' => [
                    'key' => 'placeofmarriage',
                    'grouping' => $i,
                    'value' => $details['place_of_marriage'][$i],
                    'displayName' => 'Place of Marriage'
                ],
                'occupation' => [
                    'key' => 'occupation',
                    'grouping' => $i,
                    'value' => $details['occupation_of_spouse'][$i],
                    'displayName' => 'Occupation'
                ],
                'employer' => [
                    'key' => 'employer',
                    'grouping' => $i,
                    'value' => $details['employer_of_spouse'][$i],
                    'displayName' => 'Employer'
                ]
            ];
        }

        // Mother
        if ($details['mother_last_name'] != '') {
            $entity['mother'] = [
                'lastname' => [
                    'key' => 'lastname',
                    'grouping' => null,
                    'value' => $details['mother_last_name'][$i],
                    'displayName' => 'Last Name'
                ],
                'firstname' => [
                    'key' => 'firstname',
                    'grouping' => null,
                    'value' => $details['mother_first_name'][$i],
                    'displayName' => 'First Name'
                ],
                'middlename' => [
                    'key' => 'middlename',
                    'grouping' => null,
                    'value' => $details['mother_middle_name'][$i],
                    'displayName' => 'Middle Name'
                ],
                'age' => [
                    'key' => 'age',
                    'grouping' => null,
                    'value' => $details['mother_age'][$i],
                    'displayName' => 'Age'
                ]
            ];
        }

        // Father
        if ($details['father_last_name'] != '') {
            $entity['father'] = [
                'lastname' => [
                    'key' => 'lastname',
                    'grouping' => null,
                    'value' => $details['father_last_name'][$i],
                    'displayName' => 'Last Name'
                ],
                'firstname' => [
                    'key' => 'firstname',
                    'grouping' => null,
                    'value' => $details['father_first_name'][$i],
                    'displayName' => 'First Name'
                ],
                'middlename' => [
                    'key' => 'middlename',
                    'grouping' => null,
                    'value' => $details['father_middle_name'][$i],
                    'displayName' => 'Middle Name'
                ],
                'age' => [
                    'key' => 'age',
                    'grouping' => null,
                    'value' => $details['father_age'][$i],
                    'displayName' => 'Age'
                ]
            ];
        }

        // Children
        $entity['child'] = array();
        for ($i = 0; $i < sizeof($details['child_last_name']); $i++) {

            if (sizeof($details['child_last_name']) <= 1 && $details['child_last_name'] == '')
                break;

            $entity['child'][] = [
                'lastname' => [
                    'key' => 'lastname',
                    'grouping' => $i,
                    'value' => $details['child_last_name'][$i],
                    'displayName' => 'Last Name'
                ],
                'firstname' => [
                    'key' => 'firstname',
                    'grouping' => $i,
                    'value' => $details['child_first_name'][$i],
                    'displayName' => 'First Name'
                ],
                'middlename' => [
                    'key' => 'middlename',
                    'grouping' => $i,
                    'value' => $details['child_middle_name'][$i],
                    'displayName' => 'Middle Name'
                ],
                'sex' => [
                    'key' => 'sex',
                    'grouping' => $i,
                    'value' => $details['child_sex'][$i],
                    'displayName' => 'Sex'
                ],
                'age' => [
                    'key' => 'age',
                    'grouping' => $i,
                    'value' => $details['child_age'][$i],
                    'displayName' => 'Age'
                ],
                'address' => [
                    'key' => 'address',
                    'grouping' => $i,
                    'value' => $details['child_address'][$i],
                    'displayName' => 'Address'
                ],
                'occupation' => [
                    'key' => 'occupation',
                    'grouping' => $i,
                    'value' => $details['child_occupation'][$i],
                    'displayName' => 'Occupation/Employer'
                ]
            ];
        }

        // Sibling
        $entity['sibling'] = array();
        for ($i = 0; $i < sizeof($details['sibling_last_name']); $i++) {

            if (sizeof($details['sibling_last_name']) <= 1 && $details['sibling_last_name'] == '')
                break;

            $entity['sibling'][] = [
                'lastname' => [
                    'key' => 'lastname',
                    'grouping' => $i,
                    'value' => $details['sibling_last_name'][$i],
                    'displayName' => 'Last Name'
                ],
                'firstname' => [
                    'key' => 'firstname',
                    'grouping' => $i,
                    'value' => $details['sibling_first_name'][$i],
                    'displayName' => 'First Name'
                ],
                'middlename' => [
                    'key' => 'middlename',
                    'grouping' => $i,
                    'value' => $details['sibling_middle_name'][$i],
                    'displayName' => 'Middle Name'
                ],
                'sex' => [
                    'key' => 'sex',
                    'grouping' => $i,
                    'value' => $details['sibling_sex'][$i],
                    'displayName' => 'Sex'
                ],
                'age' => [
                    'key' => 'age',
                    'grouping' => $i,
                    'value' => $details['sibling_age'][$i],
                    'displayName' => 'Age'
                ],
                'address' => [
                    'key' => 'address',
                    'grouping' => $i,
                    'value' => $details['sibling_address'][$i],
                    'displayName' => 'Address'
                ],
                'occupation' => [
                    'key' => 'occupation',
                    'grouping' => $i,
                    'value' => $details['sibling_occupation'][$i],
                    'displayName' => 'Occupation/Employer'
                ]
            ];
        }

        // References
        $entity['reference'] = array();
        for ($i = 0; $i < sizeof($details['reference_name']); $i++) {

            $entity['reference'][] = [
                'name' => [
                    'key' => 'name',
                    'grouping' => $i,
                    'value' => $details['reference_name'][$i],
                    'displayName' => 'Name'
                ],
                'occupation' => [
                    'key' => 'firstname',
                    'grouping' => $i,
                    'value' => $details['reference_occupation'][$i],
                    'displayName' => 'Occupation'
                ],
                'address' => [
                    'key' => 'address',
                    'grouping' => $i,
                    'value' => $details['reference_address'][$i],
                    'displayName' => 'Address'
                ],
                'contact' => [
                    'key' => 'contact',
                    'grouping' => $i,
                    'value' => $details['reference_contact'][$i],
                    'displayName' => 'Contact'
                ]
            ];
        }

        // Additional Information
        $entity['additionalinfo'] = [
            [
                'key' => 0,
                'grouping' => null,
                'value' => isset($details['additional_information'][0]) ? 'yes' : 'no',
                'detail' => isset($details['additional_information_detail'][0]) && $details['additional_information_detail'][0] != '' ? $details['additional_information_detail'][0] : null,
                'displayName' => 'Have you ever been found guilty or been penalized for any offense or violation involving moral turpitude or carrying the penalty of disqualification to hold public office?'
            ],
            [
                'key' => 1,
                'grouping' => null,
                'value' => isset($details['additional_information'][1]) ? 'yes' : 'no',
                'detail' => isset($details['additional_information_detail'][1]) && $details['additional_information_detail'][1] != '' ? $details['additional_information_detail'][1] : null,
                'displayName' => 'Have you been suspended, discharged, or forced to resign from any of your previous positions? If yes, provide details.'
            ],
            [
                'key' => 2,
                'grouping' => null,
                'value' => isset($details['additional_information'][2]) ? 'yes' : 'no',
                'detail' => isset($details['additional_information_detail'][2]) && $details['additional_information_detail'][2] != '' ? $details['additional_information_detail'][2] : null,
                'displayName' => 'Are you willing to accept project employment?'
            ],
            [
                'key' => 3,
                'grouping' => null,
                'value' => isset($details['additional_information'][3]) ? 'yes' : 'no',
                'detail' => isset($details['additional_information_detail'][3]) && $details['additional_information_detail'][3] != '' ? $details['additional_information_detail'][3] : null,
                'displayName' => 'Have you taken the CJI pre-employment test? If yes, please provide details.'
            ],
            [
                'key' => 4,
                'grouping' => null,
                'value' => isset($details['additional_information'][4]) ? 'yes' : 'no',
                'detail' => isset($details['additional_information_detail'][4]) && $details['additional_information_detail'][4] != '' ? $details['additional_information_detail'][4] : null,
                'displayName' => 'Do you have disablity or health condition that would affect your ability to work?'
            ]
        ];

        $entity['applicant'] = [
            'key' => 'applicant',
            'value' => '1',
            'displayName' => 'Applicant'
        ];

        return $entity;
    }

    private function employmentDetailsToEntity($details) {

        $entity = array();
        $entity['department'] = $details['department'];
        $entity['employmenttype'] = $details['employment_type'];
        $entity['contractstatus'] = $details['contract_status'];
        $entity['paymenttype'] = $details['payment_type'];
        $entity['paymentmode'] = $details['payment_mode'];

        return $entity;
    }

    private function deductiblesToEntity($details) {

        $entity = array();

        if (isset($details['tinnumber']) && $details['tinnumber'] != '') {
            $entity['tin'] = $details['tinnumber'];
        }

        if (isset($details['ssnumber']) && $details['ssnumber'] != '') {
            $entity['sss'] = $details['ssnumber'];
        }

        if (isset($details['philhealthnumber']) && $details['philhealthnumber'] != '') {
            $entity['philhealth'] = $details['philhealthnumber'];
        }

        if (isset($details['pagibignumber']) && $details['pagibignumber'] != '') {
            $entity['pagibig'] = $details['pagibignumber'];
        }

        return $entity;
    }

    private function entityToDetails($entityArray) {
        $entity = array();
        foreach ($entityArray as $arr) {
            $entity[$arr['key']] = $arr;
        }

        return $entity;
    }
}
