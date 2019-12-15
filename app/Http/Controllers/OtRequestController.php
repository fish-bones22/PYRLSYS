<?php

namespace App\Http\Controllers;

use App\Contracts\IOtRequestService;
use App\Contracts\ICategoryService;
use App\Contracts\IEmployeeService;
use App\Contracts\IManhourService;
use App\Entities\OtRequestEntity;
use Illuminate\Http\Request;

class OtRequestController extends Controller
{
    private $otRequestService;
    private $categoryService;
    private $employeeService;
    private $manhourService;
    private $pageKey = 'manhourmanagement';

    public function __construct(IOtRequestService $otRequestService, ICategoryService $categoryService, IEmployeeService $employeeService, IManhourService $manhourService) {

        $this->otRequestService = $otRequestService;
        $this->categoryService = $categoryService;
        $this->employeeService = $employeeService;
        $this->manhourService = $manhourService;
    }

    public function index(Request $request) {

        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();

        $dateFrom = $request->get('datefrom');
        $dateTo = $request->get('dateto');
        if ($dateTo == null) {
            $dateTo = $dateFrom;
        }

        $details = null;
        $departments = $this->categoryService->getCategories('department');

        if ($dateFrom == null) {
            $otRequests = $this->otRequestService->getPendingOtRequests();
        }
        else {
            $otRequests = $this->otRequestService->getPendingOtRequestsByDateRange($dateFrom, $dateTo);
            $details = array();
            $datails['datefrom'] = $dateFrom;
            $details['dateto'] = $dateTo;
        }

        return view('otrequest.index', ['otRequests' => $otRequests, 'departments' => $departments, 'details' => $details]);

    }

    public function new() {
        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();
        $departments = $this->categoryService->getCategories('department');
        return view('otrequest.new', [ 'departments' => $departments ]);
    }

    public function add(Request $request) {

        $req = $request->all();

        if ($req['date'] == null) {
            $request->flash();
            return redirect()->back()->withInputs($req)->with('error', 'Date is not set');
        }

        if ($req['department'] == null) {
            $request->flash();
            return redirect()->back()->withInputs($req)->with('error', 'Department is not set');
        }

        if ($req['date'] == null) {
            $request->flash();
            return redirect()->back()->withInputs($req)->with('error', 'Date is not set');
        }

        if (!isset($req['employee_id']) || sizeof($req['employee_id']) <= 0 || $req['employee_id'][0] == '') {
            $request->flash();
            return redirect()->back()->withInputs($req)->with('error', 'No employees selected');
        }

        if (!isset($req['ot_type']) || sizeof($req['ot_type']) <= 0 || $req['ot_type'][0] == '') {
            $request->flash();
            return redirect()->back()->withInputs($req)->with('error', 'No OT Type selected');
        }

        $errorMessages = array();

        for ($i = 0; $i < sizeof($req['employee_id']); $i++) {
            $otRequest = new OtRequestEntity();
            $otRequest->otDate = $req['date'];
            $otRequest->department = $req['employee_department'][$i];
            $otRequest->employeeName = $req['employee_name'][$i];
            $otRequest->employeeId = $req['employee_id'][$i];
            $otRequest->allowedHours = $req['allowed_hours'][$i];
            $otRequest->startTime = $req['from'][$i];
            $otRequest->endTime = $req['to'][$i];
            $otRequest->reason = $req['reason'][$i];
            $otRequest->otType = $req['ot_type'][$i];

            $result = $this->otRequestService->addOtRequest($otRequest);

            if (!$result['result']) {
                $errorMessages[] = 'Request for '. $req['employee_name'][$i].' did not save. '.$result['message'];
            }
        }

        if (sizeof($errorMessages) > 0) {
            $request->flash();
            return redirect()->back()->withInputs($req)->with('error', implode('. ', $errorMessages));
        }

        return redirect()->back()->with('success', 'OT Requests have been added.');
    }

    public function batchApprove(Request $request) {

        $batchApproval = $request->get('batchapproval');

        if ($batchApproval == null) {
            return redirect()->back()->with('error', 'No Request selected');
        }

        foreach ($batchApproval as $key => $appr) {
            $result = $this->otRequestService->approveOtRequest($key);

            if (!$result['result'])
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->back()->with('success', 'OT Requests has been approved.');
    }

    public function approve($id) {
        if ($id == 0)
            return redirect()->back()->with('error', 'Invalid record');

        $result = $this->otRequestService->approveOtRequest($id);

        if (!$result['result'])
            return redirect()->back()->with('error', $result['message']);

        return redirect()->back()->with('success', 'OT Request has been approved.');
    }

    public function deny($id) {
        if ($id == 0)
            return redirect()->back()->with('error', 'Invalid record');

        $result = $this->otRequestService->declineOtRequest($id);

        if (!$result['result'])
            return redirect()->back()->with('error', $result['message']);

        return redirect()->back()->with('success', 'OT Request has been denied.');
    }

    public function viewApproved($dateFrom, $dateTo) {

        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();

        if ($dateFrom == null)
            $dateFrom = date_create('1900-01-01');
        else
            $dateFrom = date_create($dateFrom);
        if ($dateTo == null)
            $dateTo = date_create('3000-01-01');
        else
            $dateTo = date_create($dateTo);

        $day = date_format($dateFrom,'d');
        $year = date_format($dateFrom, 'Y');
        $month = date_format($dateFrom, 'm');

        $details = array();
        $details['startday'] = $day;
        $details['endday'] =  date_format($dateTo,'d');
        $details['year'] = $year;
        $details['month'] = $month;
        $details['datefrom'] = date_format($dateFrom, 'Y-m-d');
        $details['dateto'] = date_format($dateTo, 'Y-m-d');

        $departments = $this->categoryService->getCategories('department');
        $otRequests = $this->otRequestService->getApprovedOtRequestsByDateRange(date_format($dateFrom, 'Y-m-d'), date_format($dateTo, 'Y-m-d'));
        $otRequestsDenied = $this->otRequestService->getDeniedOtRequestsByDateRange(date_format($dateFrom, 'Y-m-d'), date_format($dateTo, 'Y-m-d'));
        return view('otrequest.approvedindex', ['otRequests' => $otRequests, 'otRequestsDenied' => $otRequestsDenied, 'departments' => $departments, 'details' => $details]);
    }

    public function filterDate(Request $request) {

        $month = $request->get('month');
        $year = $request->get('year');
        $period = $request->get('period');
        $day = $period === 'first' ? '16' : '01';

        $datefrom = $request->get('datefrom');
        $dateto = $request->get('dateto');
        if ($dateto == null) {
            $dateto = $datefrom;
        }

        return redirect()->action('OtRequestController@viewApproved', ['datefrom' => $datefrom, 'dateto' => $dateto]);
    }

    public function getHolidays($date) {

        if ($date === null || $date === '') {
            return null;
        }

        $holidays = $this->manhourService->getHoliday($date);
        return response()->json($holidays);
    }

    public function getEmployees($dept) {

        $employees = $this->employeeService->getEmployeesByDepartment($dept);

        if ($employees == null) return null;

        $empJson = array();
        foreach ($employees as $emp) {
            if ($emp->inactive) continue;
            $empJson[] = [
                'id' => $emp->id,
                'name' => $emp->fullName,
                'employeeId' => $emp->employeeId,
                'timecard' => $emp->current['timecard']
            ];
        }

        return response()->json($empJson);
    }

    public function getOtRequestForEmployee($employeeId, $date) {

        $request = $this->otRequestService->getOtRequestOfEmployee($employeeId, $date);

        if ($request == null)
            return null;

        if ($request->approval === 1) {
            $approval = 'Approved';
        }
        else if ($request->approval === 0) {
            $approval = 'Denied';
        }
        else {
            $approval = 'Pending';
        }
        return response()->json([
            'allowedHours' => $request->allowedHours,
            'startTime' => $request->startTime,
            'endTime' => $request->endTime,
            'reason' => $request->reason,
            'otType' => $request->otType,
            'approval' => $approval
        ]);
    }
}
