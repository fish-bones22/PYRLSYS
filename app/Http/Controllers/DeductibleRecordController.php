<?php

namespace App\Http\Controllers;

use App\Contracts\ICategoryService;
use App\Contracts\IDeductibleRecordService;
use App\Contracts\IEmployeeService;
use App\Entities\DeductibleRecordEntity;
use Illuminate\Http\Request;

class DeductibleRecordController extends Controller
{
    private $deductibleRecordService;
    private $employeeService;
    private $categoryService;
    private $pageKey = 'payrollmanagement';

    public function __construct(IDeductibleRecordService $deductibleRecordService, IEmployeeService $employeeService, ICategoryService $categoryService) {
        $this->deductibleRecordService =  $deductibleRecordService;
        $this->employeeService = $employeeService;
        $this->categoryService = $categoryService;
    }

    public function add(Request $request, $id) {

        $req = $request->all();

        foreach ($req['models'] as $model) {

            if (!isset($model['amount']) || $model['amount'] == '')
                continue;

            $entity = $this->mapToEntity($id, $req['record_date'], $req['employee_name'], $model);

            // If entry has loan field create separate model entry
            if (isset($model['loan']) ) {

                $lnEntity = new DeductibleRecordEntity();
                $lnEntity->employee = array();
                $lnEntity->employee['id'] =  $id;
                $lnEntity->employee['name'] = $req['employee_name'];

                $lnEntity->identifier = array();
                $lnEntity->identifier['value'] = $model['identifier'];
                $lnEntity->identifier['details'] = $model['identifier_details'];

                $lnEntity->deductible = array();
                $lnEntity->id = isset($req['models'][$model['key'].'loan']['id']) ? $req['models'][$model['key'].'loan']['id'] : 0;
                $lnEntity->id = isset($req['models'][$model['key'].'loan']['id']) ? $req['models'][$model['key'].'loan']['id'] : 0;
                $lnEntity->key = $model['key'].'loan';
                $lnEntity->details = $model['details'].' Loan';
                $lnEntity->recordDate = $req['record_date'];

                $lnEntity->amount = $model['loan'];
                $result = $this->deductibleRecordService->addRecord($lnEntity);

                if (!$result['result'])
                    return redirect()->back()->withInputs($req)->with('error', $result['message']);

            }

        $result = $this->deductibleRecordService->addRecord($entity);

        if (!$result['result'])
            return redirect()->back()->withInputs($req)->with('error', $result['message']);
        }

        // Other models
        // Delete all first to avoid stale data retention
        $this->deductibleRecordService->deleteAllOtherDeductible($id, $req['record_date']);

        foreach ($req['other_models'] as $model) {

            if (!isset($model['details']) || $model['details'] == '')
                continue;

            if (!isset($model['amount']) || $model['amount'] == '')
            continue;

            $lnEntity = new DeductibleRecordEntity();

            $lnEntity->id = isset($model['id']) ? $model['id'] : 0;

            $lnEntity->employee = array();
            $lnEntity->employee['id'] =  $id;
            $lnEntity->employee['name'] = $req['employee_name'];

            $lnEntity->deductible = array();
            $lnEntity->details = $model['details'];
            $lnEntity->key = $model['details'] != null ? strtolower(str_replace($model['details'], ' ', '')) : null;
            $lnEntity->recordDate = $req['record_date'];

            $lnEntity->amount = $model['amount'];
            $lnEntity->remarks = $model['remarks'];

            $result = $this->deductibleRecordService->addRecord($lnEntity);

            if (!$result['result'])
                return redirect()->back()->withInputs($req)->with('error', $result['message']);
        }

        return redirect()->back()->with('success', 'Deductible added successfuly');

    }


    public function goToDate(Request $request, $id) {
        $month = $request->get('month');
        $year = $request->get('year');
        $period = $request->get('period');
        $day = $period === 'first' ? '16' : '01';

        return redirect()->action('DeductibleRecordController@get', ['id' => $id, 'date' => $year.'-'.$month.'-'.$day]);
    }


    public function get($id, $date) {
        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();
        $details = array();
        $details['year'] = date_format(date_create($date), 'Y');
        $details['month'] = date_format(date_create($date), 'm');
        $day = date_format(date_create($date), 'd');
        $details['startday'] = $day <= 15 ? '01' : '16';

        $records = $this->deductibleRecordService->getEmployeeDeductiblesOnDate($id, $date);
        $employee = $this->employeeService->getEmployeeById($id);
        $categories = $this->categoryService->getCategories('deductible');

        $models = array();
        $otherModels = array();

        foreach ($records as $record) {
            $model = array();

            $model['id'] = $record->id;

            $model['employee_id'] = $record->employee['id'];
            $model['employee_name'] = $record->employee['name'];

            $model['identifier'] = $record->identifier['details'];
            $model['identifier_details'] = $record->identifier['value'];

            $model['deductible_id'] = $record->deductible['id'];
            $model['duedate'] = $record->dueDate;

            $model['key'] = $record->key;
            $model['details'] = $record->details;

            $model['amount'] = $record->amount;
            $model['subamount'] = $record->subamount;
            $model['remarks'] = $record->remarks;

            if ($model['key'] === 'sss' || $model['key'] === 'sssloan'
            || $model['key'] === 'pagibig' || $model['key'] === 'pagibigloan'
            || $model['key'] === 'philhealth' || $model['key'] === 'tin') {
                if ($model['key'] != '')
                    $models[$model['key']] = $model;
                else
                    $models[] = $model;
            }
            else {
                $otherModels[] = $model;
            }
        }

        return view('deductibles.get', ['models' => $models, 'otherModels' => $otherModels, 'employee' => $employee, 'details' => $details, 'categories' => $categories]);//

    }


    private function mapToEntity($id, $date, $name, $viewModel, $entity = null) {

        if ($entity == null)
            $entity = new DeductibleRecordEntity();

        $entity->id = isset($viewModel['id']) ? $viewModel['id'] : 0;

        $entity->employee = array();
        $entity->employee['id'] =  $id;
        $entity->employee['name'] = $name;

        $entity->identifier = array();
        $entity->identifier['value'] = $viewModel['identifier'];
        $entity->identifier['details'] = $viewModel['identifier_details'];

        $entity->recordDate = $date;
        $entity->dueDate = isset($viewModel['duedate']) ? $viewModel['duedate'] : null;

        $entity->deductible = array();
        //$entity->deductible['id'] = $viewModel['id'];
        $entity->details = $viewModel['details'];
        $entity->key = $viewModel['key'];

        $entity->amount = $viewModel['amount'] != null ? $viewModel['amount'] : 0;
        $entity->subamount = isset($viewModel['subamount']) ?$viewModel['subamount'] : null;
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
        $records = $this->deductibleRecordService->getAllDeductiblesOnDate($date);

        $details = [
            'date' => $year.'-'.$month.'-'.$startDay,
            'startday' => $startDay,
            'month' => $month,
            'year' => $year
        ];

        return view('deductibles.getall', ['records' => $records, 'details' => $details]);
    }

    public function getAllOnDate(Request $request) {

        $month = $request->get('month');
        $year = $request->get('year');
        $period = $request->get('period');
        $day = $period === 'first' ? '16' : '01';

        return redirect()->action('DeductibleRecordController@getAll', ['date' => $year.'-'.$month.'-'.$day]);
    }

    public function view($key, $date) {

        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();

        $day = date_format(date_create($date),'d');
        $year = date_format(date_create($date), 'Y');
        $month = date_format(date_create($date), 'm');

        $startDay = $day <= 15 ? '01' : '16';
        $date = $year.'-'.$month.'-'.$startDay;
        $records = $this->deductibleRecordService->getAllDeductiblesOnDate($date);

        $details = [
            'date' => $year.'-'.$month.'-'.$startDay,
            'startday' => $startDay,
            'month' => $month,
            'year' => $year,
            'key' => $key
        ];

        if ($key === 'sss') {
            return view('deductibles.sss', ['records' => $records, 'details' => $details]);
        }

        return redirect()->action('DeductibleRecordController@getAll', $date);

    }

    public function goToDateView(Request $request) {

        $month = $request->get('month');
        $year = $request->get('year');
        $period = $request->get('period');
        $day = $period === 'first' ? '16' : '01';
        $key = $request->get('key');

        return redirect()->action('DeductibleRecordController@view', ['key' => $key, 'date' => $year.'-'.$month.'-'.$day]);

    }
}
