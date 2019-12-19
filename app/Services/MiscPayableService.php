<?php

namespace App\Services;

use App\Contracts\IMiscPayableService;
use App\Contracts\IEmployeeService;
use App\Entities\MiscPayableEntity;
use App\Models\MiscPayable;
use App\Http\Controllers\AuthUtility;

class MiscPayableService extends EntityService implements IMiscPayableService {

    private $employeeService;

    public function __construct(IEmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * Create or update misc. payable record given entity fields
     * particularly the record date, payable key and employee id
     */
    public function add($entity, $overwrite = true) {

        if ($entity->recordDate === null)
            return [
                'result' => false,
                'message' => 'No record date provided'
            ];

        if ($entity->key === null)
            return [
                'result' => false,
                'message' => 'No key for misc. payables provided'
            ];

        if ($entity->employee_id === null)
            return [
                'result' => false,
                'message' => 'No employee provided'
            ];

        // Try getting record
        $record = MiscPayable::where('recordDate', $entity->recordDate)->where('employee_id', $entity->employee_id)->where('key', $entity->key)->first();

        // If overwrite flag is unset
        if ($record !== null && !$overwrite) return ['result' => true];

        // Delete record if amount is 0
        if ($record !== null && (float)$entity->amount <= 0) {
            try {
                $record->delete();
                return [
                    'result' => true
                ];
            } catch (\Exception $ex) {
                return [
                    'result' => false,
                    'message' => $ex->getMessage()
                ];
            }
        }

        // Create new record if not exisiting
        if ($record === null) {
            $record = new MiscPayable();
            $record->employee_id = $entity->employee_id;
            $record->department_id = $entity->department;
            $record->key = $entity->key;
            $record->recordDate = $entity->recordDate;
            $record->employeeName = $entity->employeeName;
        }

        $record->amount = $entity->amount;
        $record->details = $entity->details;
        $record->displayName = $entity->displayName;

        try {
            $record->save();
        } catch (\Exception $ex) {
            return [
                'result' => false,
                'message' => $ex->getMessage()
            ];
        }

        return [
            'result' => true
        ];

    }


    public function getRecord($recordDate, $key) {

        if ($recordDate === null)
            return [
                'result' => false,
                'message' => 'No record date provided'
            ];

        if ($key === null)
            return [
                'result' => false,
                'message' => 'No key for misc. payables provided'
            ];


        $records = MiscPayable::where('recordDate', $recordDate)->where('key', $key)->get();
        $entities = array();
        // Iterate through records in DB and map them to model
        foreach ($records as $record) {
            // Skip departments where current user has no access to
            if (!AuthUtility::hasDepartmentAccess($record->department_id))
                continue;
            $entity = $this->mapToEntity($record, new MiscPayableEntity());
            $entities[$record->employee_id] = $entity;
        }

        return $entities;
    }


    public function getRecordByEmployee($id, $recordDate) {

        if ($recordDate === null)
            return [
                'result' => false,
                'message' => 'No record date provided'
            ];


        $records = MiscPayable::where('employee_id', $id)->where('recordDate', $recordDate)->get();
        $entities = array();
        // Iterate through records in DB and map them to model
        foreach ($records as $record) {
            // Skip departments where current user has no access to
            if (!AuthUtility::hasDepartmentAccess($record->department_id))
                continue;
            $entity = $this->mapToEntity($record, new MiscPayableEntity());
            $entities[$record->employee_id] = $entity;
        }

        return $entities;
    }


    protected function mapToEntity($model, $entity) {

        $entity = parent::mapToEntity($model, $entity);

        $entity->recordDate = $model->recordDate;
        $entity->amount = $model->amount;
        $entity->key = $model->key;
        $entity->displayName = $model->displayName;
        $entity->details = $model->details;
        $entity->timeCard = $model->timeCard;

        $entity->employeeId = $model->employee_id;
        // Get employee name
        if ($model->employee_id === null) {
            $entity->employeeName = $model->employeeName;
        }
        else {
            $entity->employeeName = $model->employee->fullName();
            $entity->employeeId = $model->employee->employeeId;
        }
        // Get department
        $entity->department = array();
        if ($model->department_id !== null) {
            $entity->department['value'] = $model->department_id;
            $entity->department['displayName'] = $model->department->value;
        }

        return $entity;
    }

}
