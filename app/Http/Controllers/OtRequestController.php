<?php

namespace App\Http\Controllers;

use App\Contracts\IOtRequestService;
use App\Contracts\ICategoryService;
use App\Contracts\IEmployeeService;
use App\Entities\OtRequestEntity;
use Illuminate\Http\Request;

class OtRequestController extends Controller
{
    private $otRequestService;
    private $categoryService;
    private $employeeService;

    public function __construct(IOtRequestService $otRequestService, ICategoryService $categoryService, IEmployeeService $employeeService) {

        $this->otRequestService = $otRequestService;
        $this->categoryService = $categoryService;
        $this->employeeService = $employeeService;
    }

    public function index() {

        $departments = $this->categoryService->getCategories('department');
        $otRequests = $this->otRequestService->getPendingOtRequests();
        return view('otrequest.index', ['otRequests' => $otRequests, 'departments' => $departments]);

    }

    public function new() {
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


    public function getEmployees($dept) {

        $employees = $this->employeeService->getEmployeesByDepartment($dept);

        if ($employees == null) return null;

        $empJson = array();
        foreach ($employees as $emp) {
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
