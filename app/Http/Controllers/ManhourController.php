<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\IManhourService;
use App\Contracts\IEmployeeService;
use App\Contracts\ICategoryService;
use App\Entities\ManhourEntity;

class ManhourController extends Controller
{
    private $manhourService;
    private $employeeService;
    private $categoryService;

    public function __construct(IManhourService $manhourService, IEmployeeService $employeeService, ICategoryService $categoryService) {
        $this->manhourService = $manhourService;
        $this->employeeService = $employeeService;
        $this->categoryService = $categoryService;
    }

    public function index() {
        $employees = $this->employeeService->getAllEmployees('lastName');
        return view("manhour.index", compact('employees'));
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
}
