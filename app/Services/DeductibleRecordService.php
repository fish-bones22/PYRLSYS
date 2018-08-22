<?php

namespace App\Services;

use App\Contracts\IDeductibleRecordService;
use App\Entities\DeductibleRecordEntity;
use App\Models\DeductibleRecord;

class DeductibleRecordService extends EntityService implements IDeductibleRecordService {


    public function addRecord($entity) {

        $model;
        if ($entity->id == 0) {
            $model = new DeductibleRecordEntity();
        }
        else {
            $model = DeductibleRecord::find($entity->id);
            if ($model == null)
                return [
                    'result' => false,
                    'message' => 'Record has been deleted'
                ];
        }

        $model->employee_id = $entity->employee['id'];
        $model->employeeName = $entity->employee['name'];
        $model->identifier = $entity->identifier['value'];
        $model->identifierDetails = $entity->identifier['details'];
        $model->deductible_id = $entity->deductible['id'];
        $model->details = $entity->deductible['details'];
        $model->amount = $entity->amount;
        $model->subamount = $entity->subamount;
        $model->remarks = $entity->remarks;

        try {
            $model->save();
        }
        catch (\Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage()
            ];
        }

    }


    public function getEmployeeDeductiblesOnDate($employeeId, $date) {

        $records = DeductibleRecord::where('employee_id', $employeeId)->where('recordDate' == $date)->get();

        if ($records == null) return null;

        $recordEntities = array();

        foreach ($records as $record) {
            $recordEntities[] = $this->mapToEntity($record, new DeductibleRecord());
        }

        return $recordEntities;

    }


    protected function mapToEntity($model, $entity) {

        $entity = parent::mapToEntity($model, $entity);

        $entity->employee = array();
        $entity->employee['id'] =  $model->employee_id;
        $entity->employee['name'] = $model->employee->fullName;

        $entity->identifier = array();
        $entity->identifier['value'] = $model->identifier;
        $entity->identifier['details'] = $model->identifierDetails;

        $entity->deductible = array();
        $entity->deductible['id'] = $model->deductible_id;
        $entity->deductible['details'] = $model->details;

        $entity->amount = $model->amount;
        $entity->subamount = $model->subamount;
        $entity->remarks = $model->remarks;

        return $entity;

    }


    public function getAllDeductiblesOnDate($date) {

        $records = DeductibleRecord::where('recordDate' == $date)->get();

        if ($records == null) return null;

        $recordEntities = array();

        foreach ($records as $record) {
            $recordEntities[] = $this->mapToEntity($record, new DeductibleRecord());
        }

        return $recordEntities;
    }


}
