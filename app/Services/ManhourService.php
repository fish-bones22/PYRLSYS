<?php

namespace App\Services;

use App\Contracts\IManhourService;
use App\Entities\ManhourEntity;
use App\Models\Manhour;

class ManhourService extends EntityService implements IManhourService {

    public function getAllRecords() {
        $records = Manhour::all();
        if ($records == null) return null;
        $recordsEntity = array();
        foreach ($records as $record) {
            $recordsEntity[] = $this->mapToEntity($record, new ManhourEntity());
        }

        return $recordsEntity;
    }


    public function getAllRecordsByDateRange($datefrom, $dateto) {

        $records = Manhour::whereBetween('recordDate', [$datefrom, $dateto])->get();

        if ($records == null) return null;

        $recordsEntity = array();
        foreach ($records as $record) {
            $recordsEntity[] = $this->mapToEntity($record, new ManhourEntity());
        }

        return $recordsEntity;
    }


    public function recordManhour(ManhourEntity $entity) {

        $record = Manhour::where('recordDate', $entity->date)->where('employee_id', $entity->employeeId)->first();

        if ($record == null) {
            $record = new Manhour();
            $record->recordDate = $entity->date;
            $record->employee_id = $entity->employeeId;
        }

        $record->timeIn = $entity->timeIn;
        $record->timeOut = $entity->timeOut;

        $record->employeeName = $entity->employeeName;
        $record->timeCard = $entity->timeCard;
        $record->department = $entity->department;

        $record->authorized = $entity->authorized;
        $record->outlier = $entity->outlier;
        $record->remarks = addslashes($entity->remarks);

        try {
            $record->save();
        } catch (\Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage()
            ];
        }

        return [
            'result' => true
        ];

    }

    protected function mapToEntity($model, $entity) {

        $entity = parent::mapToEntity($model, $entity);

        $entity->date = $model->recordDate;
        $entity->timeIn = $model->timeIn;
        $entity->timeOut = $model->timeOut;

        $entity->employeeId = $model->employee_id;
        $entity->employeeName = $model->employeeName;
        $entity->timeCard = $model->timeCard;
        $entity->department = [
            'value' => $model->department,
            'displayName' => $model->departmentDetails->value
        ];

        $entity->authorized = $model->authorized;
        $entity->outlier = $model->outlier;
        $entity->remarks = stripslashes($model->remarks);

        return $entity;

    }


    public function getRecord($id, $date) {

        $record = Manhour::where('employee_id', $id)->where('recordDate', $date)->first();

        if ($record == null)
            return null;

        return $this->mapToEntity($record, new ManhourEntity());

    }

}
