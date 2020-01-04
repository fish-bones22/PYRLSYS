<?php

namespace App\Contracts;

interface IMiscPayableService {

    public function getRecord($date, $key);
    public function add($entity);
    public function getRecordByEmployee($employeeId, $date);

}
