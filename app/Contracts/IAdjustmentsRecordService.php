<?php

namespace App\Contracts;

use App\Entities\AdjustmentsRecordEntity;

interface IAdjustmentsRecordService {

    public function addRecord($entity);
    public function getEmployeeAdjustmentsOnDate($employeeId, $date);
    public function getAllAdjustmentsOnDate($date);

}
