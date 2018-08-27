<?php

namespace App\Http\Controllers;

use App\Contracts\IAdjustmentsRecordService;
use App\Contracts\ICategoryService;
use App\Contracts\IEmployeeService;
use App\Entities\AdjustmentsRecordEntity;
use Illuminate\Http\Request;

class AdjustmentsRecordController extends Controller
{
    private $adjustmentsRecordService;
    private $employeeService;
    private $categoryService;
    private $pageKey = 'payrollmanagement';

    public function __construct(IAdjustmentsRecordService $adjustmentsRecordService,
    IEmployeeService $employeeService,
    ICategoryService $categoryService) {
        $this->adjustmentsRecordService =  $adjustmentsRecordService;
        $this->employeeService = $employeeService;
        $this->categoryService = $categoryService;
    }

    public function add(Request $request, $id) {

        $req = $request->all();

        // Delete all first to avoid stale data retention
        $this->adjustmentsRecordService->deleteAllOtherAdjustments($id, $req['record_date']);
        foreach ($req['models'] as $model) {

            if (!isset($model['amount']) || $model['amount'] == '')
                continue;

            $entity = $this->mapToEntity($id, $req['record_date'], $req['employee_name'], $model);

        $result = $this->adjustmentsRecordService->addRecord($entity);

        if (!$result['result'])
            return redirect()->back()->withInputs($req)->with('error', $result['message']);
        }


        return redirect()->back()->with('success', 'Adjustments added successfuly');

    }


    public function goToDate(Request $request, $id) {
        $month = $request->get('month');
        $year = $request->get('year');
        $period = $request->get('period');
        $day = $period === 'first' ? '16' : '01';

        return redirect()->action('AdjustmentsRecordController@get', ['id' => $id, 'date' => $year.'-'.$month.'-'.$day]);
    }


    public function get($id, $date) {

        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();

        $details = array();
        $details['year'] = date_format(date_create($date), 'Y');
        $details['month'] = date_format(date_create($date), 'm');
        $day = date_format(date_create($date), 'd');
        $details['startday'] = $day <= 15 ? '01' : '16';

        $records = $this->adjustmentsRecordService->getEmployeeAdjustmentsOnDate($id, $date);
        $employee = $this->employeeService->getEmployeeById($id);

        $models = array();
        $otherModels = array();

        $categories = $this->categoryService->getCategories('adjustments');

        foreach ($records as $record) {
            $model = array();

            $model['id'] = $record->id;

            $model['employee_id'] = $record->employee['id'];
            $model['employee_name'] = $record->employee['name'];

            $model['details'] = $record->details;

            $model['amount'] = $record->amount;
            $model['remarks'] = $record->remarks;

            $models[] = $model;
        }

        return view('adjustments.get', ['models' => $models, 'employee' => $employee, 'details' => $details, 'categories' => $categories]);

    }


    private function mapToEntity($id, $date, $name, $viewModel, $entity = null) {

        if ($entity == null)
            $entity = new AdjustmentsRecordEntity();

        $entity->id = isset($viewModel['id']) ? $viewModel['id'] : 0;

        $entity->employee = array();
        $entity->employee['id'] =  $id;
        $entity->employee['name'] = $name;

        $entity->recordDate = $date;

        $entity->details = $viewModel['details'];

        $entity->amount = $viewModel['amount'] != null ? $viewModel['amount'] : 0;
        $entity->remarks = isset($viewModel['remarks']) ?$viewModel['remarks'] : null;

        return $entity;
    }


    public function getAll($date) {
        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();
        $day = date_format(date_create($date),'d');
        $year = date_format(date_create($date), 'Y');
        $month = date_format(date_create($date), 'm');

        $startDay = $day <= 15 ? '01' : '16';
        $date = $year.'-'.$month.'-'.$startDay;
        $records = $this->adjustmentsRecordService->getAllAdjustmentsOnDate($date);

        $details = [
            'date' => $year.'-'.$month.'-'.$startDay,
            'startday' => $startDay,
            'month' => $month,
            'year' => $year
        ];

        return view('adjustments.getall', ['records' => $records, 'details' => $details]);
    }

    public function getAllOnDate(Request $request) {

        $month = $request->get('month');
        $year = $request->get('year');
        $period = $request->get('period');
        $day = $period === 'first' ? '16' : '01';

        return redirect()->action('AdjustmentsRecordController@getAll', ['date' => $year.'-'.$month.'-'.$day]);
    }
}
