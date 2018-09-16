<?php

namespace App\Services;

use App\Contracts\IDeductibleRecordService;
use App\Entities\DeductibleRecordEntity;
use App\Models\DeductibleRecord;

class DeductibleRecordService extends EntityService implements IDeductibleRecordService {


    public function addRecord($entity) {

        $model;
        if ($entity->id == 0) {
            $model = new DeductibleRecord();
        }
        else {
            $model = DeductibleRecord::find($entity->id);
            if ($model == null)
                return [
                    'result' => false,
                    'message' => 'Record has been deleted'
                ];
        }

        $model->deductible_id = isset($entity->deductible['id']) && $entity->deductible['id'] != 0 ? $entity->deductible['id'] : null;
        $model->employee_id = $entity->employee['id'];
        $model->employeeName = $entity->employee['name'];
        $model->identifier = $entity->identifier['value'];
        $model->identifierDetails = $entity->identifier['details'];
        $model->details = $entity->details;
        $model->key = $entity->key;
        $model->amount = $entity->amount;
        $model->subamount = $entity->subamount;
        $model->remarks = $entity->remarks;
        $model->recordDate = $entity->recordDate;
        $model->dueDate = $entity->dueDate;

        try {
            $model->save();
        }
        catch (\Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage()
            ];
        }

        return [
            'result' => true
        ];

    }


    public function getEmployeeDeductiblesOnDate($employeeId, $date) {

        $records = DeductibleRecord::where('employee_id', $employeeId)->where('recordDate', $date)->get();

        if ($records == null) return null;

        $recordEntities = array();

        foreach ($records as $record) {
            $recordEntities[$record->key] = $this->mapToEntity($record, new DeductibleRecordEntity());
        }

        return $recordEntities;

    }


    protected function mapToEntity($model, $entity) {

        $entity = parent::mapToEntity($model, $entity);

        $entity->employee = array();
        $entity->employee['id'] =  $model->employee_id;
        $entity->employee['employeeId'] =  $model->employee->employeeId;
        $entity->employee['name'] = $model->employee->fullName();
        $entity->employee['lastname'] = $model->employee->lastName;
        $entity->employee['firstname'] = $model->employee->firstName;
        $entity->employee['middlename'] = $model->employee->middleName;

        $entity->identifier = array();
        $entity->identifier['value'] = $model->identifier;
        $entity->identifier['details'] = $model->identifierDetails;

        $entity->deductible = array();
        $entity->deductible['id'] = $model->deductible_id;
        $entity->details = $model->details;
        $entity->key = $model->key;

        $entity->recordDate = $model->recordDate;
        $entity->dueDate = $model->dueDate;
        $entity->amount = $model->amount;
        $entity->subamount = $model->subamount;
        $entity->remarks = $model->remarks;

        return $entity;

    }


    public function getAllDeductiblesOnDate($date) {

        $records = DeductibleRecord::where('recordDate', $date)->get();

        if ($records == null) return null;

        $recordEntities = array();

        foreach ($records as $record) {
            if ($record === null || $record->employee === null)
                continue;
            $recordEntities[] = $this->mapToEntity($record, new DeductibleRecordEntity());
        }

        return $recordEntities;
    }

    public function deleteAllOtherDeductible($employeeId, $date) {

        $records = DeductibleRecord::where('employee_id', $employeeId)->where('recordDate', $date)->whereNull('identifier');

        if ($records == null) return true;

        $records->delete();

        return true;
    }


}
