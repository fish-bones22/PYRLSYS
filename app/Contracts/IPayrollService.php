<?php

namespace App\Contracts;

interface IPayrollService {

    public function getPayroll($employeeId, $date);

}
