<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\IManhourService;
use App\Contracts\IOTRequestService;
use App\Contracts\IEmployeeService;
use App\Contracts\ICategoryService;
use App\Entities\ManhourEntity;

class ManhourController extends Controller
{
    private $manhourService;
    private $otRequestService;
    private $employeeService;
    private $categoryService;

    public function __construct(IManhourService $manhourService, IOtRequestService $otRequestService, IEmployeeService $employeeService, ICategoryService $categoryService) {
        $this->manhourService = $manhourService;
        $this->otRequestService = $otRequestService;
        $this->employeeService = $employeeService;
        $this->categoryService = $categoryService;
    }

    public function index() {
        $departments = $this->categoryService->getCategories('department');
        $employees = $this->employeeService->getAllEmployees('lastName');
        return view("manhour.index", ['employees' => $employees, 'departments' => $departments ]);
    }


    public function viewNow() {
        return redirect()->route('manhour.viewrange', ['datefrom' => date_format(now(), 'Y-m-d'), 'dateto' => date_format(now(), 'Y-m-d')]);
    }


    public function viewRange($datefrom = null, $dateto = null) {

        if ($datefrom == null)
            $datefrom = date_create('1900-01-01');
        else
            $datefrom = date_create($datefrom);

        if ($dateto == null)
            $dateto = date_create('3000-01-01');
        else
            $dateto = date_create($dateto);

        $departments = $this->categoryService->getCategories('department');
        $manhours = $this->manhourService->getAllRecordsByDateRange($datefrom, $dateto);
        $records = $this->formatForView($manhours);

        $date['datefrom'] = date_format($datefrom, 'Y-m-d');
        $date['dateto'] = date_format($dateto, 'Y-m-d');
        $date['mode'] = $datefrom != $dateto ? true : false;

        return view('manhour.viewall', ['records' => $records, 'departments' => $departments, 'date' => $date ]);
    }

    public function filterDate(Request $request) {

        $mode = $request->get('mode');

        if ($mode === 'daily') {
            $date = $request->get('date');
            return redirect()->route('manhour.viewrange', ['datefrom' => $date, 'dateto' => $date]);
        }
        else {
            $month = $request->get('month');
            $year = $request->get('year');
            $datefrom = $year.'-'.$month.'-01';
            $dateto = date_format(date_create($datefrom), 'Y-m-t');
            return redirect()->route('manhour.viewrange', ['datefrom' => $datefrom, 'dateto' => $dateto]);
        }
    }


    public function getNext($id) {

        $employees = $this->employeeService->getAllEmployees('lastName');
        $newId = null;

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


    public function input($id = null) {

        if ($id == null) {
            return redirect()->action('ManhourController@getNext', 0);
        }

        $employee = $this->employeeService->getEmployeeById($id);

        if ($employee == null)
            return redirect()->action('ManhourController@index');

        $outliers = $this->categoryService->getCategories('outlier');

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

        $manhourEntity->employeeId = $id;
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

        return redirect()->action('ManhourController@getNext', $id);
    }

    public function getRecord($id, $date) {

        if ($id == null || $id == 0 || $date == null || $date == '')
            return null;

        $record = $this->manhourService->getRecord($id, $date);

        if ($record == null)
            return null;

        return response()->json([
            'timeIn' => $record->timeIn,
            'timeOut' => $record->timeOut,
            'outlier' => $record->outlier,
            'remarks'  => $record->remarks,
            'authorized' => $record->authorized
        ]);
    }


    private function formatForView($entities) {

        $viewModels = array();
        foreach ($entities as $entity) {

            $viewModel = array();
            $viewModel['timecard'] = $entity->timeCard;
            $viewModel['employeeName'] = $entity->employeeName;
            $viewModel['department'] = $entity->department['displayName'];

            $date = date_create($entity->date);
            $employee = $this->employeeService->getEmployeeById($entity->employeeId);
            $otRequest = $this->otRequestService->getApprovedOtRequestByDateRange($entity->employeeId, $date, $date);

            if ($employee == null)
                return;

            $viewModel['date'] = date_format($date, 'M d Y');
            $hours = 0;
            $otHours = 0;
            $overtimeCounted = false;
            if ($entity->timeIn != null && $entity->timeOut != null) {
                // Get employee schedule
                $scheduledTimeIn = key_exists('timein', $employee->details) ? date_create($employee->details['timein']['value']) : null;
                $scheduledTimeOut = key_exists('timeout', $employee->details) ? date_create($employee->details['timeout']['value']) : null;
                // Get scheduled time in/out in Time object
                $scheduledTimeIn_ = key_exists('timein', $employee->details) ? strtotime($employee->details['timein']['value']) : null;
                $scheduledTimeOut_ =  key_exists('timeout', $employee->details) ? strtotime($employee->details['timeout']['value']) : null;
                // Get time in/out in Date object
                $timeIn = date_create($entity->timeIn);
                $timeOut = date_create($entity->timeOut);
                // Get time in/out in Time object
                $timeIn_ = strtotime($entity->timeIn);
                $timeOut_ = strtotime($entity->timeOut);
                // Get time to use (either scheduled or actual)
                $actualTimeIn = $timeIn_;
                $actualTimeOut = $timeOut_;
                if ($scheduledTimeIn_ != null && $timeIn_ <= $scheduledTimeIn_) {
                    $actualTimeIn = $scheduledTimeIn_;
                }
                if ($scheduledTimeOut_ != null && $timeOut_ >= $scheduledTimeOut_) {
                    $actualTimeOut = $scheduledTimeOut_;
                }

                // Get time in/out recorded hour
                $x = ($timeOut_ - $timeIn_) / 3600;
                $recordHour = floor($x * 2) / 2;
                $recordHour = $recordHour < 0 ? (24 + $recordHour) : $recordHour;

                // Get scheduled hour
                $x = $scheduledTimeIn_ != null && $scheduledTimeOut_ != null ? ($scheduledTimeOut_ - $scheduledTimeIn_) / 3600 : null;
                $scheduledHour = $x != null ? floor($x * 2) / 2 : null;
                $scheduledHour = $scheduledHour != null && $scheduledHour < 0 ? (24 + $scheduledHour) : $scheduledHour;

                // Get actual hours
                $x = ($actualTimeOut - $actualTimeIn) / 3600;
                $actualHours = floor($x * 2) / 2;
                $actualHours = $actualHours < 0 ? (24 + $actualHours) : $actualHours;

                $viewModel['timeIn'] = date_format($timeIn, 'h:i A');
                $viewModel['timeOut'] = date_format($timeOut, 'h:i A');
                $viewModel['undertime'] = '';
                // If Under time
                if ($scheduledTimeOut != null) {
                    if ($scheduledTimeOut > $timeOut) {
                        $viewModel['timeOut'] = '';
                        $viewModel['undertime'] = date_format($timeOut, 'h:i A');
                    }
                }

                // Check if OT is counted
                if($otRequest != null) {

                    // Get OT start and end time
                    $otStartTime_ = strtotime($otRequest[0]->startTime);
                    $otEndTime_ = strtotime($otRequest[0]->endTime);

                    if ($timeOut_ > $scheduledTimeOut_ && $otStartTime_ < $timeOut_) {
                        $overtimeCounted = true;
                        $otHours =  $otRequest[0]->allowedHours;

                        // Get actual hours in OT
                        $otActualHours = ($timeOut_ - $otStartTime_) / 3600;
                        $otActualHours = floor($otActualHours * 2) / 2;
                        $otActualHours = $otActualHours < 0 ? (24 + $otActualHours) : $otActualHours;

                        if ($otActualHours < $otHours) {
                            $otHours = $otActualHours;
                        }

                    }
                }
            }
            else {
                $viewModel['timeIn'] = 'A';
                $viewModel['timeOut'] = '';
                $viewModel['undertime'] = '';
            }

            $viewModel['hours'] = $actualHours;

            $viewModel['rot'] = '';
            $viewModel['sot'] = '';
            $viewModel['xsot'] = '';
            $viewModel['lhot'] = '';
            $viewModel['xlhot'] = '';
            if ($otRequest != null && $overtimeCounted) {
                if ($otRequest[0]->otType == 'rot') {
                    $viewModel['rot'] = $otHours;
                }
                else if ($otRequest[0]->otType == 'sot') {
                    $viewModel['sot'] = $otHours;
                }
                else if ($otRequest[0]->otType == 'xsot') {
                    $viewModel['xsot'] = $otHours;
                }
                else if ($otRequest[0]->otType == 'lhot') {
                    $viewModel['lhot'] = $otHours;
                }
                else if ($otRequest[0]->otType == 'xlhot') {
                    $viewModel['xlhot'] = $otHours;
                }
            }
            $viewModel['remarks'] = $entity->remarks;

            $viewModels[] = $viewModel;
        }

        return $viewModels;
    }
}
