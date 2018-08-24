<?php

namespace App\Http\Controllers;

use App\Contracts\IDeductibleRecordService;
use App\Contracts\IEmployeeService;
use App\Entities\DeductibleRecordEntity;
use Illuminate\Http\Request;

class DeductibleRecordController extends Controller
{
    private $deductibleRecordService;
    private $employeeService;

    public function __construct(IDeductibleRecordService $deductibleRecordService, IEmployeeService $employeeService) {
        $this->deductibleRecordService =  $deductibleRecordService;
        $this->employeeService = $employeeService;
    }

    public function add(Request $request, $id) {

        $req = $request->all();

        foreach ($req['models'] as $model) {

            if (!isset($model['amount']) || $model['amount'] == '')
                continue;

            $entity = $this->mapToEntity($id, $req['record_date'], $req['employee_name'], $model);

            // If entry has loan field create separate model entry
            if (isset($model['loan'])) {

                $lnEntity = new DeductibleRecordEntity();
                $lnEntity->employee = array();
                $lnEntity->employee['id'] =  $model['employee_id'];
                $lnEntity->employee['name'] = $model['employee_name'];

                $lnEntity->identifier = array();
                $lnEntity->identifier['value'] = $model['identifier'];
                $lnEntity->identifier['details'] = $model['identifier_details'];

                $lnEntity->deductible = array();
                $lnEntity->deductible['details'] = $model['details'].'loan';

                $lnEntity->amount = $model['loan'];
                $result = $deductibleRecordService->add($lnEntity);

                if (!$result['result'])
                    return redirect()->back()->withInputs($model)->with('error', $result->message);

            }

        $result = $deductibleRecordService->add($entity);

        if (!$result['result'])
                return redirect()->back()->withInputs($model)->with('error', $result->message);

        }

        return redirect()->back()->with('success');

    }


    public function goToDate(Request $request, $id) {
        $month = $request->get('month');
        $year = $request->get('year');
        $period = $request->get('period');
        $day = $period === 'first' ? '17' : '01';

        return redirect()->action('DeductibleRecordController@get', ['id' => $id, 'date' => $year.'-'.$month.'-'.$day]);
    }


    public function get($id, $date) {

        $details = array();
        $details['year'] = date_format(date_create($date), 'Y');
        $details['month'] = date_format(date_create($date), 'm');
        $day = date_format(date_create($date), 'd');
        $details['startday'] = $day <= 16 ? '01' : '17';

        $records = $this->deductibleRecordService->getEmployeeDeductiblesOnDate($id, $date);
        $employee = $this->employeeService->getEmployeeById($id);

        $models = array();

        foreach ($records as $record) {
            $model = array();

            $model['id'] = $record->id;

            $model['employee_id'] = $record->employee['id'];
            $model['employee_name'] = $record->employee['name'];

            $model['identifier'] = $record->identifier['details'];
            $model['identifier_details'] = $record->identifier['value'];

            $model['deductible_id'] = $record->deductible['id'];
            $model['details'] = $record->deductible['details'];

            $model['amount'] = $record->amount;
            $model['subamount'] = $record->subamount;
            $model['remarks'] = $record->remarks;

            $models[] = $model;
        }

        $otherModels = array();
        return view('deductibles.get', ['models' => $models, 'otherModels' => $otherModels, 'employee' => $employee, 'details' => $details]);//

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

        $entity->deductible = array();
        $entity->deductible['id'] = $viewModel['id'];
        $entity->deductible['details'] = $viewModel['details'];

        $entity->amount = $viewModel['amount'] != null ? $viewModel['amount'] : 0;
        $entity->subamount = isset($viewModel['subamount']) ?$viewModel['subamount'] : null;
        $entity->remarks = isset($viewModel['remarks']) ?$viewModel['remarks'] : null;

        return $entity;
    }
}
