<?php

namespace App\Http\Controllers;

use App\Contracts\ICategoryService;
use App\Contracts\IDeductibleRecordService;
use App\Contracts\IEmployeeService;
use App\Contracts\IPayrollService;
use App\Entities\DeductibleRecordEntity;
use Illuminate\Http\Request;

class DeductibleRecordController extends Controller
{
    private $deductibleRecordService;
    private $employeeService;
    private $categoryService;
    private $payrollService;
    private $pageKey = 'payrollmanagement';

    public function __construct(IDeductibleRecordService $deductibleRecordService, IEmployeeService $employeeService, ICategoryService $categoryService, IPayrollService $payrollService) {
        $this->deductibleRecordService =  $deductibleRecordService;
        $this->employeeService = $employeeService;
        $this->categoryService = $categoryService;
        $this->payrollService = $payrollService;
    }

    public function add(Request $request, $id) {

        $req = $request->all();

        foreach ($req['models'] as $model) {

            // if (!isset($model['amount']) )//|| $model['amount'] == '')
            //     continue;
            if (!isset($model['identifier']) )//|| $model['amount'] == '')
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
            $lnEntity->key = $model['details'] != null ? strtolower(str_replace(' ', '', $model['details'])) : null;
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
        $properDate = $details['year'] . '-' . $details['month'] . '-' . $details['startday'];

        $records = $this->deductibleRecordService->getEmployeeDeductiblesOnDate($id, $properDate);
        $employee = $this->employeeService->getEmployeeById($id);
        $categories = $this->categoryService->getCategories('deductible');
        $payroll = $this->payrollService->getBasicPay($id, date_create($properDate));


        $details['basic'] = $payroll->basicPay;
        $details['rate'] = $payroll->rate;
        $details['gross'] = $payroll->grossPay;
        $details['basis'] = $payroll->rateBasis;

        $models = array();
        $otherModels = array();

        foreach ($records as $record) {

            $model = array();

            $model['id'] = $record->id;

            $model['employee_id'] = $record->employee['id'];
            $model['employee_name'] = $record->employee['name'];

            $model['identifier'] = $record->identifier['value'];
            $model['identifier_details'] = $record->identifier['details'];

            $model['deductible_id'] = $record->deductible['id'];
            $model['duedate'] = $record->dueDate;

            $model['key'] = $record->key;
            $model['details'] = $record->details;

            $model['amount'] = $record->amount;
            $model['subamount'] = $record->subamount;
            $model['subamount2'] = $record->subamount2;
            $model['remarks'] = $record->remarks;

            $models[$record->key] = $model;
        }

        $rem = $this->payrollService->getRemittanceDeductible($id, $date);
        // SSS
        if (!isset($models['sss'])) {
            $models['sss'] = array();
        }
        if (!isset($models['sss']['amount']) || $models['sss']['amount'] == null) {
            $models['sss']['amount'] = isset($rem['sss']) ? $rem['sss'][0] : 0;
            if ($models['sss']['amount'] != 0)
                $models['sss']['auto'] = true;
        }
        if (!isset($models['sss']['subamount']) || $models['sss']['subamount'] == null) {
            $models['sss']['subamount'] = isset($rem['sss']) ? $rem['sss'][1] : 0;
            if ($models['sss']['subamount'] != 0)
                $models['sss']['auto2'] = true;
        }
        if (!isset($models['sss']['subamount2']) || $models['sss']['subamount2'] == null) {
            $models['sss']['subamount2'] = isset($rem['sss']) ? $rem['sss'][2] : 0;
            if ($models['sss']['subamount2'] != 0)
                $models['sss']['auto3'] = true;
        }
        // Philhealth

        if (!isset($models['philhealth'])) {
            $models['philhealth'] = array();
        }
        if (!isset($models['philhealth']['amount']) || $models['philhealth']['amount'] == null) {
            $models['philhealth']['amount'] = isset($rem['philhealth']) ? $rem['philhealth'][0] : 0;
            if ($models['philhealth']['amount'] != 0)
                $models['philhealth']['auto'] = true;
        }
        if (!isset($models['philhealth']['subamount']) || $models['philhealth']['subamount'] == null) {
            $models['philhealth']['subamount'] = isset($rem['philhealth']) ? $rem['philhealth'][1] : 0;
            if ($models['philhealth']['subamount'] != 0)
                $models['philhealth']['auto2'] = true;
        }
        // PAGIBIG
        if (!isset($models['pagibig'])) {
            $models['pagibig'] = array();
        }
        if (!isset($models['pagibig']['amount']) || $models['pagibig']['amount'] == null) {
            $models['pagibig']['amount'] = isset($rem['pagibig']) ? $rem['pagibig'][0] : 0;
            if ($models['pagibig']['amount'] != 0)
                $models['pagibig']['auto'] = true;
        }
        if (!isset($models['pagibig']['subamount']) || $models['pagibig']['subamount'] == null) {
            $models['pagibig']['subamount'] = isset($rem['pagibig']) ? $rem['pagibig'][1] : 0;
            if ($models['pagibig']['subamount'] != 0)
                $models['pagibig']['auto2'] = true;
        }
        // Tax
        if (!isset($models['tin'])) {
            $models['tin'] = array();
        }
        if (!isset($models['tin']['amount']) || $models['tin']['amount'] == null) {
            $models['tin']['amount'] = isset($rem['tin']) ? $rem['tin'][0] : 0;
            if ($models['tin']['amount'] != 0)
                $models['tin']['auto'] = true;
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

        $entity->amount = isset($viewModel['amount']) ? $viewModel['amount'] : null;
        $entity->subamount = isset($viewModel['subamount']) ?$viewModel['subamount'] : null;
        $entity->subamount2 = isset($viewModel['subamount2']) ?$viewModel['subamount2'] : null;
        $entity->remarks = isset($viewModel['remarks']) ?$viewModel['remarks'] : null;

        return $entity;
    }

    public function autogenerate(Request $request, $date) {

        $year = date_format(date_create($date), 'Y');
        $month = date_format(date_create($date), 'm');
        $day = date_format(date_create($date), 'd');

        $day = (((int)$day) < 16) ? '1' : '16';
        $date = $year.'-'.$month.'-'.$day;
        $req = $request->all();
        $override = isset($req['override_values']) ? true : false;

        $employees = $this->employeeService->getAllEmployees();

        foreach ($employees as $employee) {
            $id = $employee->id;
            $rem = $this->payrollService->getRemittanceDeductible($id, $date);
            $records = $this->deductibleRecordService->getEmployeeDeductiblesOnDate($id, $date);

            $hasSSS = false;
            $hasPagibig = false;
            $hasPhilhealth = false;
            $hasTax = false;

            foreach ($records as $record) {

                if ($record->key != 'sss'
                && $record->key != 'pagibig'
                && $record->key != 'philhealth'
                && $record->key != 'tin') {
                    continue;
                }

                $entity = new DeductibleRecordEntity();

                $entity->id = $record->id;

                $entity->employee = array();
                $entity->employee['id'] = $record->employee['id'];
                $entity->employee['name'] = $record->employee['name'];

                $entity->identifier = array();
                $entity->identifier['value'] = $record->identifier['details'];
                $entity->identifier['details'] = $record->identifier['value'];

                $entity->recordDate = $date;
                $entity->dueDate = $record->dueDate;

                $entity->deductible = array();
                $entity->details = $record->details;
                $entity->key = $record->key;

                $entity->amount = $record->amount;
                $entity->subamount = $record->subamount;
                $entity->subamount2 = $record->subamount2;
                $entity->remarks = $record->remarks;

                // SSS
                if ($record->key == 'sss') {
                    if ( $override) {
                        $entity->amount = isset($rem['sss']) ? $rem['sss'][0] : 0;
                        $entity->subamount = isset($rem['sss']) ? $rem['sss'][1] : 0;
                        $entity->subamount2 = isset($rem['sss']) ? $rem['sss'][2] : 0;
                    }
                    $hasSSS = true;
                }
                // Philhealth
                else if ($record->key == 'philhealth') {
                    if ( $override) {
                        $entity->amount = isset($rem['philhealth']) ? $rem['philhealth'][0] : 0;
                        $entity->subamount = isset($rem['philhealth']) ? $rem['philhealth'][1] : 0;
                    }
                    $hasPhilhealth = true;
                }
                // Pagibig
                else if ($record->key == 'pagibig') {
                    if ( $override) {
                        $entity->amount = isset($rem['pagibig']) ? $rem['pagibig'][0] : 0;
                        $entity->subamount = isset($rem['pagibig']) ? $rem['pagibig'][1] : 0;
                    }
                    $hasPagibig = true;
                }
                // Tax
                else if ($record->key == 'tin') {
                    if ( $override) {
                        $entity->amount = isset($rem['tin']) ? $rem['tin'][0] : 0;
                    }
                    $hasTax = true;
                }
                $result = $this->deductibleRecordService->addRecord($entity);
            }
            // Set records if not existing yet
            // SSS
            if (isset($employee->deductibles['sss']) && !$hasSSS) {
                $entity = new DeductibleRecordEntity();
                $entity->employee = array();
                $entity->employee['id'] = $employee->id;
                $entity->employee['name'] = $employee->fullName;;
                $entity->identifier = array();
                $entity->identifier['value'] = $employee->deductibles['sss'];
                $entity->identifier['details'] = 'SS Number';
                $entity->recordDate = $date;
                $entity->deductible = array();
                $entity->details = 'SSS';
                $entity->key = 'sss';
                $entity->amount = isset($rem['sss']) ? $rem['sss'][0] : 0;
                $entity->subamount = isset($rem['sss']) ? $rem['sss'][1] : 0;
                $entity->subamount2 = isset($rem['sss']) ? $rem['sss'][2] : 0;
                $result = $this->deductibleRecordService->addRecord($entity);
            }
            // Philhealth
            if (isset($employee->deductibles['philhealth']) && !$hasPhilhealth) {
                $entity = new DeductibleRecordEntity();
                $entity->employee = array();
                $entity->employee['id'] = $employee->id;
                $entity->employee['name'] = $employee->fullName;;
                $entity->identifier = array();
                $entity->identifier['value'] = $employee->deductibles['philhealth'];
                $entity->identifier['details'] = 'PhilHealth Number';
                $entity->recordDate = $date;
                $entity->deductible = array();
                $entity->details = 'PhilHealth';
                $entity->key = 'philhealth';
                $entity->amount = isset($rem['philhealth']) ? $rem['philhealth'][0] : 0;
                $entity->subamount = isset($rem['philhealth']) ? $rem['philhealth'][1] : 0;
                $result = $this->deductibleRecordService->addRecord($entity);
            }
            // PAGIBIG
            if (isset($employee->deductibles['pagibig']) && !$hasPagibig) {
                $entity = new DeductibleRecordEntity();
                $entity->employee = array();
                $entity->employee['id'] = $employee->id;
                $entity->employee['name'] = $employee->fullName;;
                $entity->identifier = array();
                $entity->identifier['value'] = $employee->deductibles['pagibig'];
                $entity->identifier['details'] = 'PAGIBIG Number';
                $entity->recordDate = $date;
                $entity->deductible = array();
                $entity->details = 'PAGIBIG';
                $entity->key = 'pagibig';
                $entity->amount = isset($rem['pagibig']) ? $rem['pagibig'][0] : 0;
                $entity->subamount = isset($rem['pagibig']) ? $rem['pagibig'][1] : 0;
                $result = $this->deductibleRecordService->addRecord($entity);
            }
            // Tax
            if (isset($employee->deductibles['tin']) && !$hasTax) {
                $entity = new DeductibleRecordEntity();
                $entity->employee = array();
                $entity->employee['id'] = $employee->id;
                $entity->employee['name'] = $employee->fullName;;
                $entity->identifier = array();
                $entity->identifier['value'] = $employee->deductibles['tin'];
                $entity->identifier['details'] = 'TIN';
                $entity->recordDate = $date;
                $entity->deductible = array();
                $entity->details = 'Withholding Tax';
                $entity->key = 'tin';
                $entity->amount = isset($rem['tin']) ? $rem['tin'][0] : 0;
                $entity->subamount = isset($rem['tin']) ? $rem['tin'][1] : 0;
                $result = $this->deductibleRecordService->addRecord($entity);
            }
        }

        return redirect()->action('DeductibleRecordController@getAll', ['date' => $date]);

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

        $day2 = date_format(date_create($date),'d');
        $year2 = date_format(date_create($date), 'Y');
        $month2 = date_format(date_create($date), 'm');

        if ($day > 15) {
            $day = '16';
            $day2 = '01';
            $month2 = $month < 12 ? $month + 1 : '01';
            $year2 = $month < 12 ? $year : $year + 1;
        }
        else {
            $day = '16';
            $day2 = '01';
            $month = $month > 1 ? $month - 1 : '12';
            $year = $month > 1 ? $year : $year - 1;
        }

        $date = $year.'-'.$month.'-'.$day;
        $date2 = $year2.'-'.$month2.'-'.$day2;

        $records = $this->deductibleRecordService->getAllDeductiblesOnDate($date);
        $records2 = $this->deductibleRecordService->getAllDeductiblesOnDate($date2);
        $departments = $this->categoryService->getCategories('department');

        $details = [
            'date' => $year.'-'.$month.'-'.$day,
            'date2' => $year2.'-'.$month2.'-'.$day2,
            'startday' => $day,
            'month' => $month,
            'year' => $year,
            'startday2' => $day2,
            'month2' => $month2,
            'year2' => $year2,
            'key' => $key
        ];

        if ($key == 'all') {
            return view('deductibles.item.overall', ['records' => $records, 'records2' => $records2, 'details' => $details, 'departments' => $departments]);
        }
        if ($key == 'tin') {
            $payrollRecord1 = array();
            $payrollRecord2 = array();
            foreach ($records as $record) {
                $payrollRecord1[$record->employee['id']] = $this->payrollService->getPayroll($record->employee['id'], date_create($date));
            }
            foreach ($records2 as $record) {
                $payrollRecord2[$record->employee['id']] = $this->payrollService->getPayroll($record->employee['id'],  date_create($date2));
            }
            return view('deductibles.item.bir', ['records' => $records, 'records2' => $records2, 'details' => $details, 'departments' => $departments, 'payrollRecord1' => $payrollRecord1, 'payrollRecord2' => $payrollRecord2 ]);
        }
        if ($key == 'companyloan' || $key == 'companyloan/cashadvance') {
            return view('deductibles.item.clca', ['records' => $records, 'records2' => $records2, 'details' => $details, 'departments' => $departments]);
        }
        if ($key == 'mealdeduction' || $key == 'medicaldeduction') {
            $name = 'Deduction';
            if ($key == 'mealdeduction') {
                $name = "Meal Deduction";
            }
            if ($key == 'medicaldeduction') {
                $name = "Medical Deduction";
            }
            return view('deductibles.item.meal', ['name' => $name, 'records' => $records, 'records2' => $records2, 'details' => $details, 'departments' => $departments]);
        }
        if ($key != 'all') {
            return view('deductibles.item.sss', ['records' => $records, 'records2' => $records2, 'details' => $details, 'departments' => $departments]);
        }

        return redirect()->action('DeductibleRecordController@getAll', $date);

    }

    public function goToDateView(Request $request) {

        $month = $request->get('month');
        $year = $request->get('year');
        $day = '16';
        $key = $request->get('key');

        return redirect()->action('DeductibleRecordController@view', ['key' => $key, 'date' => $year.'-'.$month.'-'.$day]);

    }

}
