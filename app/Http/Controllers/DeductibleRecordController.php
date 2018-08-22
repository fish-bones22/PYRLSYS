<?php

namespace App\Http\Controllers;

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

            $entity = new DeductibleRecordEntity();
            $entity->id = isset($model['id']) ? $model['id'] : 0;

            $entity->employee = array();
            $entity->employee['id'] =  $model['employee_id'];
            $entity->employee['name'] = $model['employee_name'];

            $entity->identifier = array();
            $entity->identifier['value'] = $model['identifier'];
            $entity->identifier['details'] = $model['identifierDetails'];

            $entity->deductible = array();
            $entity->deductible['id'] = $model['deductible_id'];
            $entity->deductible['details'] = $model['details'];

            $entity->amount = $model['amount'];
            $entity->subamount = $model['subamount'];
            $entity->remarks = $model['remarks'];

        $result = $deductibleRecordService->add($entity);

        if (!$result['result'])
                return redirect()->back()->withInputs($model)->with('error', $result->message);

        }

        return redirect()->back()->with('success');

    }
}
