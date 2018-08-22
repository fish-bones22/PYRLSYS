<?php

namespace App\Contracts;

use App\Entities\DeductibleRecordEntity;

interface IDeductibleRecordService {

    public function addRecord($entity);
    public function getEmployeeDeductiblesOnDate($employeeId, $date);
    public function getAllDeductiblesOnDate($date);

}
