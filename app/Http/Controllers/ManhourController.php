<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\IManhourService;
use App\Contracts\IOTRequestService;
use App\Contracts\IEmployeeService;
use App\Contracts\ICategoryService;
use App\Entities\ManhourEntity;
use Carbon\Carbon;

class ManhourController extends Controller
{
    private $manhourService;
    private $otRequestService;
    private $employeeService;
    private $categoryService;
    private $pageKey = 'manhourmanagement';

    public function __construct(IManhourService $manhourService, IOtRequestService $otRequestService, IEmployeeService $employeeService, ICategoryService $categoryService) {
        $this->manhourService = $manhourService;
        $this->otRequestService = $otRequestService;
        $this->employeeService = $employeeService;
        $this->categoryService = $categoryService;
    }

    public function index() {
        $departments = $this->categoryService->getCategories('department');
        $employees = $this->employeeService->getAllEmployees('lastname');
        return view("manhour.index", ['employees' => $employees, 'departments' => $departments ]);
    }


    public function viewNow() {
        return redirect()->route('manhour.viewrange', ['mode' => 'daily', 'datefrom' => date_format(now(), 'Y-m-d'), 'dateto' => date_format(now(), 'Y-m-d')]);
    }


    public function viewRange($mode, $datefrom = null, $dateto = null) {

        if ($datefrom == null)
            $datefrom = date_create('1900-01-01');
        else
            $datefrom = date_create($datefrom);

        if ($dateto == null)
            $dateto = date_create('3000-01-01');
        else
            $dateto = date_create($dateto);

        $departments = $this->categoryService->getCategories('department');
        $records = $this->manhourService->getSummaryOfRecordsByDateRange($datefrom, $dateto);

        // if ($records == null || sizeof($records) == 0 || $records[0] == null)
        //     return redirect()->action('ManhourController@index');

        $date['datefrom'] = date_format($datefrom, 'Y-m-d');
        $date['dateto'] = date_format($dateto, 'Y-m-d');
        $date['startday'] = date_format($datefrom, 'd');
        $date['mode'] = $mode;

        return view('manhour.viewall_', ['records' => $records, 'departments' => $departments, 'date' => $date ]);
    }


    public function viewRecordNow($id) {
        $month = date_format(now(), 'm');
        $day = date_format(now(), 'j');
        $year = date_format(now(), 'Y');
        return redirect()->action('ManhourController@viewRecord', ['id' => $id, 'year' => $year, 'month' => $month, 'day' => $day]);
    }


    public function setRecordDate(Request $request, $id) {
        $year = $request->get('year');
        $month = $request->get('month');
        $day = '01';
        $period = $request->get('period');
        if ($period !== 'second')
            $day = '16';

        return redirect()->action('ManhourController@viewRecord', ['id' => $id,'year' => $year, 'month' => $month, 'day' => $day]);

    }

    public function setRecordDateCollated(Request $request) {
        $year = $request->get('year');
        $month = $request->get('month');
        $day = '01';
        $period = $request->get('period');
        if ($period !== 'second')
            $day = '16';

        return redirect()->action('ManhourController@viewRecordCollated', ['year' => $year, 'month' => $month, 'day' => $day]);

    }

    public function viewRecord($id, $year = null, $month = null, $day = null) {

        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();

        $startDay;
        $endDay;
        if ($day <= 15) {
            $startDay = '1';
            $endDay = 15;
        }
        else {
            $startDay = 16;
            $endDay = date_format(date_create($year.'-'.$month.'-'.$endDay), 't'); // End of month
        }
        $datefrom = date_create($year.'-'.$month.'-'.$startDay);
        $dateto = date_create($year.'-'.$month.'-'.$endDay);
        $details = array();
        $employee = $this->employeeService->getEmployeeById($id);

        if ($employee == null)
            return redirect()->action('ManhourController@index');
        $details['startday'] = $startDay;
        $details['endday'] = $endDay;
        $details['year'] = $year;
        $details['month'] = $month;
        $details['id'] = $id;
        $details['employeeId'] = $employee->employeeId;
        $details['lastname'] = $employee->lastName;
        $details['firstname'] = $employee->firstName;
        $details['middlename'] = $employee->middleName;
        $details['name'] = $employee->fullName;

        $records = array();

        for ($i = $startDay; $i <= $endDay; $i++) {
            $record = $this->manhourService->getSummaryOfRecord($id, $year.'-'.$month.'-'.$i, $employee);
            $records[$i] = $record;
        }
        return view('manhour.viewindividual', ['records' => $records, 'details' => $details]);
    }


    public function viewRecordCollated($year = null, $month = null, $day = null) {

        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();

        $startDay;
        $endDay;
        if ($day <= 15) {
            $startDay = '1';
            $endDay = 15;
        }
        else {
            $startDay = 16;
            $endDay = date_format(date_create($year.'-'.$month.'-'.$startDay), 't'); // End of month
        }
        $datefrom = date_create($year.'-'.$month.'-'.$startDay);
        $dateto = date_create($year.'-'.$month.'-'.$endDay);
        $details = array();

        $employees =  $this->employeeService->getAllEmployees();
        $records = array();

        $details['startday'] = $startDay;
        $details['endday'] = $endDay;
        $details['year'] = $year;
        $details['month'] = $month;
        $details['employees'] = array();

        foreach ($employees as $employee) {

            if ($employee == null)
                return redirect()->action('ManhourController@index');

            $emp = array();
            $emp['id'] = $employee->id;
            $emp['employeeId'] = $employee->employeeId;
            $emp['lastname'] = $employee->lastName;
            $emp['firstname'] = $employee->firstName;
            $emp['middlename'] = $employee->middleName;
            $emp['name'] = $employee->fullName;
            $details['employees'][$employee->id] = $emp;

            $employeeRecord = array();

            for ($i = $startDay; $i <= $endDay; $i++) {
                $record = $this->manhourService->getSummaryOfRecord($employee->id, $year.'-'.$month.'-'.$i, $employee);
                $employeeRecord[$i] = $record;
            }

            $records[] = $employeeRecord;

        }

        $departments = $this->categoryService->getCategories('department');
        return view('manhour.viewcollated', ['records' => $records, 'details' => $details, 'departments' => $departments]);
    }


    public function filterDate(Request $request) {

        $mode = $request->get('mode');

        if ($mode === 'daily') {
            $date = $request->get('date');
            return redirect()->route('manhour.viewrange', ['mode' => $mode, 'datefrom' => $date, 'dateto' => $date]);
        }
        else if ($mode === 'periodic') {
            $period = $request->get('period');
            $month = $request->get('month_period');
            $year = $request->get('year_period');

            $startDay = $period === 'second' ? '01' : '16';
            $datefrom = $year.'-'.$month.'-'.$startDay;

            $endDay = $period === 'second' ? '15' : date_format(date_create($datefrom), 't');
            $dateto = $year.'-'.$month.'-'.$endDay;

            return redirect()->route('manhour.viewrange', ['mode' => $mode, 'datefrom' => $datefrom, 'dateto' => $dateto]);
        }
        else {
            $month = $request->get('month');
            $year = $request->get('year');
            $datefrom = $year.'-'.$month.'-01';
            $dateto = date_format(date_create($datefrom), 'Y-m-t');
            return redirect()->route('manhour.viewrange', ['mode' => $mode, 'datefrom' => $datefrom, 'dateto' => $dateto]);
        }
    }


    public function search(Request $request, $id) {
        $req = $request->all();

        if (isset($req["search"]) && $req["search"] != '') {
            $employees = $this->employeeService->getEmployeeByName($req["search"]);
            if ($employees != null) {
                $id = $employees->id;
            }
        }

        return redirect()->action('ManhourController@input', ['id' => $id]);
    }


    public function getNext($id) {

        $employees = $this->employeeService->getAllEmployees('lastName');
        $newId = null;

        if ($employees == null || sizeof($employees) === 0) {
            return redirect()->action('ManhourController@index');
        }

        for ($i = 0; $i < sizeof($employees); $i++) {

            if ($employees[$i]->id != $id)
                continue;

            if ($i == sizeof($employees) - 1)
                break;

            $newId = $employees[$i+1]->id;

            break;
        }

        if ($newId == null)
            $newId = $employees[0]->id;

        return redirect()->action('ManhourController@input', ['id' => $newId]);
    }

    public function getPrev($id) {

        $employees = $this->employeeService->getAllEmployees('lastName');
        $newId = null;

        if ($employees == null || sizeof($employees) === 0) {
            return redirect()->action('ManhourController@index');
        }

        for ($i = sizeof($employees)-1; $i >= 0; $i--) {

            if ($employees[$i]->id != $id)
                continue;

            if ($i == 0)
                break;

            $newId = $employees[$i-1]->id;

            break;
        }

        if ($newId == null)
            $newId = $employees[sizeof($employees)-1]->id;

        return redirect()->action('ManhourController@input', ['id' => $newId]);
    }


    public function input($id = null) {

        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();

        if ($id == null) {
            return redirect()->action('ManhourController@getNext', 0);
        }

        $employee = $this->employeeService->getEmployeeById($id);

        if ($employee == null)
            return redirect()->action('ManhourController@index');

        $outliers = $this->categoryService->getCategories('outlier');

        if (session()->has('success')) {
            $message = session()->get('success');
            session()->forget('success');
            return view('manhour.input', ['employee' => $employee, 'outliers' => $outliers])->with('success', $message);
        }


        return view('manhour.input', ['employee' => $employee, 'outliers' => $outliers]);
    }


    public function record(Request $request, $id) {

        $req = $request->all();

        if ($req['time_card'] == '')
            return redirect()->back()->withInputs($req)->with('error', 'No timecard');

        if ($req['date'] == '')
            return redirect()->back()->withInputs($req)->with('error', 'Select a date on the calendar');

        if (!isset($req['outlier']) || $req['outlier'] == '') {
            if ($req['time_in'] == '')
                return redirect()->back()->withInputs($req)->with('error', 'Provide time in data');
            if ($req['time_out'] == '')
                return redirect()->back()->withInputs($req)->with('error', 'Provide time out data');
        }

        $manhourEntity = new ManhourEntity();

        $manhourEntity->date = $req['date'];
        $manhourEntity->timeIn = $req['time_in'];
        $manhourEntity->timeOut = $req['time_out'];

        $manhourEntity->employee_id = $id;
        $manhourEntity->employeeName = $req['full_name'];
        $manhourEntity->timeCard = $req['time_card'];
        $manhourEntity->department = $req['department'];

        if (isset($req['outlier']) && $req['outlier'] != '') {
            $manhourEntity->authorized = isset($req['authorized']) ? true : false;
        }

        $manhourEntity->outlier = isset($req['outlier']) ? $req['outlier'] : null;
        $manhourEntity->remarks = $req['remarks'];

        $result = $this->manhourService->recordManhour($manhourEntity);

        if (!$result['result']) {
            return redirect()->back()->withInputs($req)->with('error', $result['message']);
        }

        session()->put('success', 'Manhour record is successfully added');
        return redirect()->action('ManhourController@getNext', $id);
    }


    public function inputAll($date) {

        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();

        if ($date == null)
            $date = now();
        else
            $date = date_create($date);

        $departments = $this->categoryService->getCategories('department');
        $records = array();//$this->manhourService->getSummaryOfRecordsByDateRange($date, $date);
        $outliers = $this->categoryService->getCategories('outlier');
        $employees = $this->employeeService->getAllEmployees();

        if ($employees != null && sizeof($employees) > 0) {
            foreach ($employees as $employee) {
                $records[] = $this->manhourService->getSummaryOfRecord($employee->id, $date, $employee);
            }
        }

        // if ($records == null || sizeof($records) == 0 || $records[0] == null)
        //     return redirect()->action('ManhourController@index');

        $details['date'] = date_format($date, 'Y-m-d');

        return view('manhour.inputall', ['records' => $records, 'departments' => $departments, 'details' => $details, 'outliers' => $outliers, 'employees' => $employees ]);
    }


    public function recordAll(Request $request) {

        $req = $request->all();

        //for ($i = 0; $i < sizeof($req['time_in']); $i++) {
        foreach ($req['time_in'] as $i => $value) {
            // if (!isset($req['time_in'][$i])) {
            //     continue;
            // }

            $manhourEntity = new ManhourEntity();

            $manhourEntity->date = $req['date'];
            $manhourEntity->timeIn = $req['time_in'][$i];

            if (isset($req['time_out'][$i]) && $req['time_out'][$i] != null) {
                $manhourEntity->timeOut = $req['time_out'][$i];
            }
            else {
                $manhourEntity->timeOut = $req['time_out_undertime'][$i];
            }

            $manhourEntity->employee_id = $req['employee_id'][$i];
            $manhourEntity->employeeName = $req['employee_name'][$i];
            $manhourEntity->timeCard = $req['time_card'][$i];
            $manhourEntity->department = $req['department'][$i];

            if (isset($req['outlier'][$i]) && $req['outlier'][$i] != '') {
                $manhourEntity->authorized = isset($req['authorized'][$i]) ? true : false;
            }

            $manhourEntity->outlier = isset($req['outlier'][$i]) ? $req['outlier'][$i] : null;
            $manhourEntity->remarks = $req['remarks'][$i];

            $result = $this->manhourService->recordManhour($manhourEntity);

            if (!$result['result']) {
                return redirect()->back()->withInputs($req)->with('error', $result['message']);
            }

        }
        return redirect()->back()->withInputs($req)->with('success', 'Success');
    }

    public function filterDateAll(Request $request) {

        $date = $request->get('date');
        return redirect()->route('manhour.inputall', ['date' => $date]);
    }


    public function getRecord($id, $date) {

        if ($id == null || $id == 0 || $date == null || $date == '')
            return null;

        $record = $this->manhourService->getRecord($id, $date);
        $empDetails = $this->employeeService->getEmployeeHistoryOnDate($id, date_create($date));

        return response()->json([
            'timeIn' => $record != null ? $record->timeIn : null,
            'timeOut' => $record != null ? $record->timeOut : null,
            'outlier' => $record != null && $record->outlier != null ? $record->outlier['value'] : null,
            'remarks'  => $record != null ? $record->remarks : null,
            'authorized' => $record != null ? $record->authorized : null,
            'timeCard' => $empDetails['timecard'],
            'departmentName' => $empDetails['department']['displayName'],
            'departmentId' => $empDetails['department']['value']
        ]);
    }

}
