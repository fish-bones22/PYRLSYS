<?php

namespace App\Services;

use App\Contracts\IAdjustmentsRecordService;
use App\Entities\AdjustmentsRecordEntity;
use App\Models\AdjustmentsRecord;

class AdjustmentsRecordService extends EntityService implements IAdjustmentsRecordService {


    public function addRecord($entity) {

        $model;
        if ($entity->id == 0) {
            $model = new AdjustmentsRecord();
        }
        else {
            $model = AdjustmentsRecord::find($entity->id);
            if ($model == null)
                return [
                    'result' => false,
                    'message' => 'Record has been deleted'
                ];
        }

        $model->employee_id = $entity->employee['id'];
        $model->employeeName = $entity->employee['name'];
        $model->details = $entity->details;
        $model->amount = $entity->amount;
        $model->remarks = $entity->remarks;
        $model->recordDate = $entity->recordDate;

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


    public function getEmployeeAdjustmentsOnDate($employeeId, $date) {

        $records = AdjustmentsRecord::where('employee_id', $employeeId)->where('recordDate', $date)->get();

        if ($records == null) return null;

        $recordEntities = array();

        foreach ($records as $record) {
            $recordEntities[$record->details] = $this->mapToEntity($record, new AdjustmentsRecordEntity());
        }

        return $recordEntities;

    }


    protected function mapToEntity($model, $entity) {

        $entity = parent::mapToEntity($model, $entity);

        $entity->employee = array();
        $entity->employee['id'] =  $model->employee_id;
        $entity->employee['employeeId'] =  $model->employee->employeeId;
        $entity->employee['name'] = $model->employee->fullName();

        $entity->adjustments = array();
        $entity->details = $model->details;

        $entity->amount = $model->amount;
        $entity->remarks = $model->remarks;

        return $entity;

    }


    public function getAllAdjustmentsOnDate($date) {

        $records = AdjustmentsRecord::where('recordDate', $date)->get();

        if ($records == null) return null;

        $recordEntities = array();

        foreach ($records as $record) {
            $recordEntities[] = $this->mapToEntity($record, new AdjustmentsRecordEntity());
        }

        return $recordEntities;
    }

    public function deleteAllOtherAdjustments($employeeId, $date) {

        $records = AdjustmentsRecord::where('employee_id', $employeeId)->where('recordDate', $date);

        if ($records == null) return true;

        $records->delete();

        return true;
    }


}
