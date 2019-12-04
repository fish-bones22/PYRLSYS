<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Intervention\Image\ImageManagerStatic as Image;

use App\Contracts\ICategoryService;
use App\Contracts\IEmployeeService;
//use App\Models\Employee;
//use App\Models\EmployeeHistory;
//use App\Models\EmployeePicture;
use App\Entities\EmployeeEntity;

use PDF;

use Dompdf\Dompdf;

class EmployeeController extends Controller
{
    protected $employeeService;
    protected $categoryService;
    private $pageKey = 'humanresourcemanagement';

    public function __construct(IEmployeeService $employeeService, ICategoryService $categoryService)
    {

        $this->employeeService = $employeeService;
        $this->categoryService = $categoryService;
    }

    public function index()
    {

        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();
        $employees = $this->employeeService->getAllEmployees();
        $departments = $this->categoryService->getCategories('department');

        return view('employee.index', ['employees' => $employees, 'departments' => $departments]);
    }

    public function new()
    {
        return redirect()->action('EmployeeController@show', 0);
    }

    public function view($id)
    {
        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();

        $categories = array();

        $categories['department'] = $this->categoryService->getCategories('department');
        $categories['employmenttype'] = $this->categoryService->getCategories('employmenttype');
        $categories['contractstatus'] = $this->categoryService->getCategories('contractstatus');
        $categories['paymenttype'] = $this->categoryService->getCategories('paymenttype');
        $categories['paymentmode'] = $this->categoryService->getCategories('paymentmode');

        if ($id == 0) {
            return view('employee.view', ['employee' => new EmployeeEntity(), 'categories' => $categories]);
        }

        $employee = $this->employeeService->getEmployeeById($id);

        if ($employee == null)
            return redirect()->action('EmployeeController@index');


        return view('employee.view', ['employee' => $employee, 'categories' => $categories]);
    }

    public function show($id = 0)
    {

        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();
        $categories = array();

        $categories['department'] = $this->categoryService->getCategories('department');
        $categories['employmenttype'] = $this->categoryService->getCategories('employmenttype');
        $categories['contractstatus'] = $this->categoryService->getCategories('contractstatus');
        $categories['paymenttype'] = $this->categoryService->getCategories('paymenttype');
        $categories['paymentmode'] = $this->categoryService->getCategories('paymentmode');

        if ($id == 0) {
            return view('employee.show', ['employee' => new EmployeeEntity(), 'categories' => $categories]);
        }

        $employee = $this->employeeService->getEmployeeById($id);

        if ($employee == null)
            return redirect()->action('EmployeeController@index');

        return view('employee.show', ['employee' => $employee, 'categories' => $categories]);
    }

    /*
     * This method is for updating or adding an employee
     * @var $request
     * @var $id
     * */
    public function update(Request $request, $id)
    {

        $req = $request->all();

        $employee = new EmployeeEntity;
        $employee->id = $id;
        $employee->firstName = $req['first_name'];
        $employee->lastName = $req['last_name'];
        $employee->middleName = $req['middle_name'];
        $employee->employeeId = $req['employee_id'];
        $employee->sex = $req['sex'];

        // Backend validations
        if (!isset($req['time_card'])) {
            return redirect()->action('EmployeeController@show', $id)->with('error', 'Time card required');
        }

        // Save file
        if ($request->file_new != null) {

            $req['file_new_name'] = array();

            foreach ($request->file('file_new') as $file) {

                if($file === null)
                {
                   continue;
                }

                $newFileNamesForDb = "";

                // creating a filename
                $filename = $employee->employeeId . '-' . $file->getClientOriginalName();
                // Store file to storage
                Storage::putFileAs('public/files/', $file, $filename, 'public');

                $newFileNamesForDb = $filename;

                $req['file_new_name'][] = $newFileNamesForDb;

            }
        }

        $employee->details = $this->detailsToEntity($req);
        $employee->current = $this->historyToEntity($req);
        $employee->timeTable = $this->timeTableToEntity($req);
        $employee->payTable = $this->payTableToEntity($req);

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
            $result = $this->employeeService->idExists($req['employee_id']);

            if ($result) {
                return redirect()->action('EmployeeController@show', $id)->withinput($req)->with('error', 'Employee Id already exists');
            }

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
                $filename = time() . $id . '.' . $image->getClientOriginalExtension();
                $this->saveImageToStorage($image, $location, $filename);
                // Save data to DB
                $this->employeeService->addEmployeeImage($id, $location, $filename);
            }
        }


        return redirect()->action('EmployeeController@show', $id)->with('success', 'Successfully ' . $action . ' employee. ');
    }

    public function destroy($id)
    {

        $result = $this->employeeService->removeEmployee($id);

        if (!$result['result'])
            return redirect()->action('EmployeeController@index')->with('error', $result['message']);

        return redirect()->action('EmployeeController@index')->with('success', 'Delete successful');
    }

    public function deleteAll()
    {
        $this->employeeService->deleteAllEmployee();
        return redirect()->action('EmployeeController@index')->with('success', 'Deleted all successful');
    }

    public function deleteAllInactive()
    {
        $res = $this->employeeService->deleteInactive();
        if (!$res['result']) {
            return redirect()->action('EmployeeController@index')->with('error', $res['message']);
        }
        return redirect()->action('EmployeeController@index')->with('success', 'Deleted all successful');
    }

    public function transferEmployee(Request $request, $id)
    {

        $req  = $request->all();
        $employmentDetails = $this->historyToEntity($req);
        $res = $this->employeeService->transferEmployee($id, $employmentDetails);

        if (!$res['result'])
            return redirect()->back()->with('error', 'Transfer failed. ' . $res['message']);

        return redirect()->back()->with('success', 'Transfer success');
    }

    public function updateImage(Request $request, $id)
    {

        if (
            !$request->file('picture_file')
            && !$request->get('selected_filename')
            && !$request->get('selected_location')
        ) {
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
            $filename = time() . $id . '.' . $image->getClientOriginalExtension();

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

    public function deleteImage(Request $request, $id)
    {

        $location = $request->get('location');
        $filename = $request->get('filename');

        $this->removeImageFromStorage($location . $filename);

        $this->employeeService->removeEmployeeImage($id, $location, $filename);

        return redirect()->action('EmployeeController@show', $id);
    }

    public function getEmployeeBasicDetails($id)
    {

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
            'department' => $employee->current['department']['displayName'],
            'departmentValue' => $employee->current['department']['value'],
            'timecard' => $employee->current['timecard']
        ]);
    }

    public function downloadFile($filename)
    {
        return Storage::download('public/files/' . $filename);
    }

    public function downloadAllFiles($employeeId)
    {
        $zipFileName = storage_path("app/public/". $employeeId . '-files.zip');
        $zip = \Zipper::make($zipFileName);

        $files = storage_path('app/public/files/'. $employeeId .'*');
        $zip->add(glob($files));
        $zip->close();
        return response()->download($zipFileName);
    }

    public function getEmployeeJson($id)
    {

        $employee = $this->employeeService->getEmployeeById($id);
        return json_encode($employee);
    }

    private function saveImageToStorage($file, $location, $filename)
    {
        // Resize amd crop image to square
        $resizedImg = Image::make($file);
        $resizedImg = $this->resizeImage($resizedImg);
        // Store to file to storage
        Storage::put('public/' . $location . $filename, (string) $resizedImg->encode());
    }

    private function removeImageFromStorage($path)
    {
        Storage::delete('public/' . $path);
    }

    private function resizeImage($image)
    {

        $size = 300;
        $newWidth = $image->width();
        $newHeight = $image->height();

        // if image is portrait
        if ($newHeight > $newWidth) {
            $newHeight = ($newHeight / $newWidth) * $size;
            $newWidth = $size;
        } else { // if landscape
            $newWidth = ($newWidth / $newHeight) * $size;
            $newHeight = $size;
        }

        $image->resize($newWidth, $newHeight)->crop($size, $size);
        return $image;
    }

    private function payTableToEntity($payTable) {
        $entity= array();

        $entity = array();
        $entity['id'] = isset($payTable['schedule_id']) ? $payTable['schedule_id'] : null;
        $entity['rate'] = isset($payTable['rate']) ? $payTable['rate'] : null;
        $entity['ratebasis'] = isset($payTable['rate_basis']) ? $payTable['rate_basis'] : null;
        $entity['allowance'] = isset($payTable['allowance']) ? $payTable['allowance'] : null;
        $entity['startdate'] = isset($payTable['effective_date_start_pay']) ? $payTable['effective_date_start_pay'] : null;
        $entity['enddate'] = isset($payTable['effective_date_end_pay']) ? $payTable['effective_date_end_pay'] : null;
        $entity['paymentmode'] = isset($payTable['payment_mode']) ? $payTable['payment_mode'] : null;

        return $entity;
    }

    private function timeTableToEntity($history)
    {

        $entity = array();
        $entity['id'] = isset($history['schedule_id']) ? $history['schedule_id'] : null;
        $entity['timein'] = isset($history['time_in']) ? $history['time_in'] : null;
        $entity['timeout'] = isset($history['time_out']) ? $history['time_out'] : null;
        $entity['startdate'] = isset($history['effective_date_start']) ? $history['effective_date_start'] : null;
        $entity['enddate'] = isset($history['effective_date_end']) ? $history['effective_date_end'] : null;
        $entity['break'] = isset($history['break']) ? $history['break'] : null;

        return $entity;
    }

    private function historyToEntity($history)
    {

        $entity = array();

        // Time card
        $entity['timecard'] =  $history['time_card'];

        // Position
        $entity['position'] = $history['position'];

        // Date hired
        $entity['datestarted'] = $history['date_started'];

        // Date End
        $entity['datetransfered'] = $history['date_transfered'];

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

        return $entity;
    }

    private function detailsToEntity($details)
    {

        $entity = array();

        // Civil status
        $entity['civilstatus'] = [
            'key' => 'civilstatus',
            'value' => $details['civil_status'],
            'displayName' => 'Civil Status'
        ];

        // Birthday
        $entity['birthday'] = [
            'key' => 'birthday',
            'value' => $details['birthday'],
            'displayName' => 'Birthday'
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
        for ($i = 0; $i < sizeof($details['dependent_last_name']); $i++) {

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

        // Address
        $entity['presentaddress'] = [
            'key' => 'presentaddress',
            'value' => $details['presentaddress'],
            'displayName' => 'Address'
        ];
        /*
         * Added Phone number 1 and 2
         */
        // Phone Number 1
        $entity['phonenumber1'] = [
            'key' => 'phonenumber1',
            'value' => $details['phone_number_1'],
            'displayName' => 'Phone Number 1'
        ];
        // Phone Number 2
        $entity['phonenumber2'] = [
            'key' => 'phonenumber2',
            'value' => $details['phone_number_2'],
            'displayName' => 'Phone Number 2'
        ];

        // Email
        $entity['emailaddress'] = [
            'key' => 'emailaddress',
            'value' => $details['email_address'],
            'displayName' => 'Email Address'
        ];

        // Emergency Person Name
        $entity['emergencyname'] = [
            'key' => 'emergencyname',
            'value' => $details['emergency_name'],
            'displayName' => 'Name'
        ];

        // Emergency Person Number
        $entity['emergencyphone'] = [
            'key' => 'emergencyphone',
            'value' => $details['emergency_phone'],
            'displayName' => 'Hourly Rate'
        ];

        // File
        $entity['file'] = array();
        // Save new files
        if (isset($details['file_new_name'])) {
            for ($i = 0; $i < sizeof($details['file_new_name']); $i++) {

                if ($details['file_new_name'][$i] == null)
                    continue;

                $entity['file'][] = [
                    'filename' => [
                        'key' => 'filename',
                        'grouping' => $i,
                        'value' => $details['file_new_name'][$i],
                        'displayName' => 'File Name'
                    ],
                    'details' => [
                        'key' => 'details',
                        'grouping' => $i,
                        'value' => $details['file_new_details'][$i],
                        'displayName' => 'File Details'
                    ]
                ];
            }

        }
        $curSize = sizeof($entity['file']);
        // Update old files
        for ($i = 0; $i < sizeof($details['file_old']); $i++) {

            if ($details['file_old'][$i] == null)
                continue;

            $entity['file'][] = [
                'filename' => [
                    'key' => 'filename',
                    'grouping' =>  $curSize + $i,
                    'value' => $details['file_old'][$i],
                    'displayName' => 'File Name'
                ],
                'details' => [
                    'key' => 'details',
                    'grouping' => $i,
                    'value' => $details['file_details'][$i],
                    'displayName' => 'File Details'
                ]
            ];
        }

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

    private function employmentDetailsToEntity($details)
    {

        $entity = array();
        $entity['department'] = $details['department'];
        $entity['employmenttype'] = $details['employment_type'];
        $entity['contractstatus'] = $details['contract_status'];
        $entity['paymenttype'] = $details['payment_type'];
        $entity['paymentmode'] = $details['payment_mode'];

        return $entity;
    }

    private function deductiblesToEntity($details)
    {

        $entity = array();

        $entity['tin'] = array();
        $entity['sss'] = array();
        $entity['philhealth'] = array();
        $entity['pagibig'] = array();

        $entity['tin']['isset'] = true;
        $entity['sss']['isset'] = true;
        $entity['philhealth']['isset'] = true;
        $entity['pagibig']['isset'] = true;

        // TIN
        if (isset($details['tinnumber'])) {
            $entity['tin']['value'] = $details['tinnumber'];
        }
        if (isset($details['tin'])) {
            $entity['tin']['isset'] = true;
        }

        // SSS
        if (isset($details['ssnumber'])) {
            $entity['sss']['value'] = $details['ssnumber'];
        }
        if (isset($details['sss'])) {
            $entity['sss']['isset'] = true;
        }

        // Philhealth
        if (isset($details['philhealthnumber'])) {
            $entity['philhealth']['value'] = $details['philhealthnumber'];
        }
        if (isset($details['philhealth'])) {
            $entity['philhealth']['isset'] = true;
        }

        // PAGIBIG
        if (isset($details['pagibignumber'])) {
            $entity['pagibig']['value'] = $details['pagibignumber'];
        }
        if (isset($details['pagibig'])) {
            $entity['pagibig']['isset'] = true;
        }

        return $entity;
    }

    private function entityToDetails($entityArray)
    {
        $entity = array();
        foreach ($entityArray as $arr) {
            $entity[$arr['key']] = $arr;
        }

        return $entity;
    }


}
