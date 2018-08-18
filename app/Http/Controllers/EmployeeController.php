<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Intervention\Image\ImageManagerStatic as Image;

use App\Contracts\ICategoryService;
use App\Contracts\IEmployeeService;
use App\Models\Employee;
use App\Models\EmployeeHistory;
use App\Models\EmployeePicture;
use App\Entities\EmployeeEntity;

class EmployeeController extends Controller
{
    protected $employeeService;
    protected $categoryService;

    public function __construct(IEmployeeService $employeeService, ICategoryService $categoryService) {

        $this->employeeService = $employeeService;
        $this->categoryService = $categoryService;

    }

    public function index() {

        $employees = $this->employeeService->getAllEmployees();

        return view('employee.index', compact('employees'));

    }


    public function new() {

        return redirect()->action('EmployeeController@show', 0);
    }

    public function show($id = 0) {

        $categories = array();

        $categories['department'] = $this->categoryService->getCategories('department');
        $categories['employmenttype'] = $this->categoryService->getCategories('employmenttype');
        $categories['contractstatus'] = $this->categoryService->getCategories('contractstatus');
        $categories['paymenttype'] = $this->categoryService->getCategories('paymenttype');
        $categories['paymentmode'] = $this->categoryService->getCategories('paymentmode');

        if ($id == 0) {
            return view('employee.show', ['employee' => new EmployeeEntity(), 'categories' => $categories]);
        }

        $employee= $this->employeeService->getEmployeeById($id);

        if ($employee == null)
            return redirect()->action('EmployeeController@index');


        return view('employee.show', ['employee' => $employee, 'categories' => $categories]);

    }


    public function update(Request $request, $id) {

        $req = $request->all();

        $employee = new EmployeeEntity;
        $employee->id = $id;
        $employee->firstName = $req['first_name'];
        $employee->lastName = $req['last_name'];
        $employee->middleName = $req['middle_name'];
        $employee->employeeId = $req['employee_id'];
        $employee->sex = $req['sex'];

        $employee->details = $this->detailsToEntity($req);
        $employee->current = $this->historyToEntity($req);

        //$employee->employmentDetails = $this->employmentDetailsToEntity($req);

        $employee->deductibles = $this->deductiblesToEntity($req);
        $action = 'updated';
        // Update
        if ($id != 0) {
            $result = $this->employeeService->updateEmployee($employee);
            if (!$result['result']) {
                return redirect()->action('EmployeeController@show', $id)->with('error', $result['message']);
            }
        }
        // Add
        else {

            $action = 'added';
            $result = $this->employeeService->addEmployee($employee);
            if (!$result['result']) {
                return redirect()->action('EmployeeController@show', $id)->with('error', $result['message']);
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


        return redirect()->action('EmployeeController@show', $id)->with('success', 'Successfully $action employee. ');

    }

    public function destroy($id) {

        $result = $this->employeeService->removeEmployee($id);

        if (!$result['result'])
            return redirect()->action('EmployeeController@index')->with('error', $result['message']);

        return redirect()->action('EmployeeController@index')->with('success', '');

    }

    public function transferEmployee(Request $request, $id) {

        $req  = $request->all();
        $employmentDetails = $this->historyToEntity($req);
        $res = $this->employeeService->transferEmployee($id, $employmentDetails);

        if (!$res['result'])
            return redirect()->back()->with('error', 'Transfer failed. '.$res['message']);

        return redirect()->back()->with('success', 'Transfer success');
    }

    public function updateImage(Request $request, $id) {

        if (!$request->file('picture_file')
        && !$request->get('selected_filename')
        && !$request->get('selected_location')) {
            return redirect()->action('EmployeeController@show', $id)->with('error', 'Image file is not valid');
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

        return redirect()->action('EmployeeController@show', $id)
        ->with('success', 'Image successfully changed');
    }


    public function deleteImage(Request $request, $id) {

        $location = $request->get('location');
        $filename = $request->get('filename');

        $this->removeImageFromStorage($location.$filename);

        $this->employeeService->removeEmployeeImage($id, $location, $filename);

        return redirect()->action('EmployeeController@show', $id);
    }



    public function getEmployeeBasicDetails($id) {

        if ($id == 0) return null;

        $employee = $this->employeeService->getEmployeeById($id);

        if ($employee == null) return null;

        return response()->json([
            'id' => $employee->id,
            'employeeId' => $employee->employeeId,
            'lastName' => $employee->lastName,
            'firstName' => $employee->firstName,
            'middleName' => $employee->middleName,
            'fullName' => $employee->fullName,
            'department' => $employee->employmentDetails['department']['displayName'],
            'departmentValue' => $employee->employmentDetails['department']['value'],
            'timecard' => $employee->details['timecard']['value'],
        ]);

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


    private function historyToEntity($history) {

        $entity = array();

        // Time card
        $entity['timecard'] =  $history['time_card'];

        // Position
        $entity['position'] = $history['position'];

        // Date hired
        $entity['datestarted'] = $history['date_started'];

        // Date End
        $entity['datetransfered'] = $history['date_transfered'];

        // Rate
        $entity['rate'] = $history['rate'];

        // Allowance
        $entity['allowance'] = $history['allowance'];

        // Time In
        $entity['timein'] = $history['time_in'];

        // Time Out
        $entity['timeout'] = $history['time_out'];

        // Department
        $entity['department'] = [
            'key' => 'department',
            'value' => $history['department']
        ];

        // Employment Type
        $entity['employmenttype'] = [
            'key' => 'employmenttype',
            'value' => $history['employment_type']
        ];

        // Status
        $entity['contractstatus'] = [
            'key' => 'contractstatus',
            'value' => $history['contract_status']
        ];

        // Payment Type
        $entity['paymenttype'] = [
            'key' => 'paymenttype',
            'value' => $history['payment_type']
        ];

        // Payment Type
        $entity['paymentmode'] = [
            'key' => 'paymenttype',
            'value' => $history['payment_mode']
        ];

        return $entity;

    }


    private function detailsToEntity($details) {

        $entity = array();

        // Civil status
        $entity['civilstatus'] = [
            'key' => 'civilstatus',
            'value' => $details['civil_status'],
            'displayName' => 'Civil Status'
        ];

        // Spouse
        $entity['spouse'] = array();
        for ($i = 0; $i < sizeof($details['spouse_last_name']); $i++) {

            if ($details['spouse_last_name'][$i] == null)
                continue;

            $entity['spouse'][] = [
                'lastname' => [
                    'key' => 'lastname',
                    'grouping' => 0,
                    'value' => $details['spouse_last_name'][$i],
                    'displayName' => 'Last Name'
                ],
                'firstname' => [
                    'key' => 'firstname',
                    'grouping' => 0,
                    'value' => $details['spouse_first_name'][$i],
                    'displayName' => 'First Name'
                ],
                'middlename' => [
                    'key' => 'middlename',
                    'grouping' => 0,
                    'value' => $details['spouse_middle_name'][$i],
                    'displayName' => 'Middle Name'
                ]
            ];
        }

        // Dependent
        $entity['dependent'] = array();
        for($i = 0; $i < sizeof($details['dependent_last_name']); $i++) {

            if ($details['dependent_last_name'][$i] == null)
                continue;

            $entity['dependent'][] = [
                'lastname' => [
                    'key' => 'lastname',
                    'grouping' => $i,
                    'value' => $details['dependent_last_name'][$i],
                    'displayName' => 'Last Name'
                ],
                'firstname' => [
                    'key' => 'firstname',
                    'grouping' => $i,
                    'value' => $details['dependent_first_name'][$i],
                    'displayName' => 'First Name'
                ],
                'middlename' => [
                    'key' => 'middlename',
                    'grouping' => $i,
                    'value' => $details['dependent_middle_name'][$i],
                    'displayName' => 'Middle Name'
                ],
                'relationship' => [
                    'key' => 'relationship',
                    'grouping' => $i,
                    'value' => $details['dependent_relationship'][$i],
                    'displayName' => 'Relationship'
                ]
            ];
        }

        // Time card
        // $entity['timecard'] = [
        //     'key' => 'timecard',
        //     'value' => $details['time_card'],
        //     'displayName' => 'Time Card'
        // ];

        // // Position
        // $entity['position'] = [
        //     'key' => 'position',
        //     'value' => $details['position'],
        //     'displayName' => 'Position'
        // ];

        // // Date hired
        // $entity['datehired'] = [
        //     'key' => 'datehired',
        //     'value' => $details['date_hired'],
        //     'displayName' => 'Date Hired'
        // ];

        // // Date End
        // $entity['dateend'] = [
        //     'key' => 'dateend',
        //     'value' => $details['date_end'],
        //     'displayName' => 'Date End'
        // ];

        // // Date hired
        // $entity['rate'] = [
        //     'key' => 'rate',
        //     'value' => $details['rate'],
        //     'displayName' => 'Hourly Rate'
        // ];

        // // Allowance
        // $entity['allowance'] = [
        //     'key' => 'allowance',
        //     'value' => $details['allowance'],
        //     'displayName' => 'Allowance'
        // ];

        // // Time In
        // $entity['timein'] = [
        //     'key' => 'timein',
        //     'value' => $details['time_in'],
        //     'displayName' => 'Time In'
        // ];

        // // Time Out
        // $entity['timeout'] = [
        //     'key' => 'timeout',
        //     'value' => $details['time_out'],
        //     'displayName' => 'Time Out'
        // ];

        // Number of Memo
        $entity['numberofmemo'] = [
            'key' => 'numberofmemo',
            'value' => $details['number_of_memo'],
            'displayName' => 'Number of Memo'
        ];

        // Remarks
        $entity['remarks'] = [
            'key' => 'remarks',
            'value' => $details['remarks'],
            'displayName' => 'Remarks'
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

        if (isset($details['tin'])) {
            $entity['tin'] = $details['tinnumber'];
        }

        if (isset($details['sss'])) {
            $entity['sss'] = $details['ssnumber'];
        }

        if (isset($details['philhealth'])) {
            $entity['philhealth'] = $details['philhealthnumber'];
        }

        if (isset($details['pagibig'])) {
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
