<?php

namespace App\Services;

use App\Contracts\IPayrollService;

class PayrollService implements IPayrollService {

    public $payrollService;
    public $employeeService;
    public $manhourService;

    public function __construct (IPayrollService $payrollService, IEmployeeService $employeeService, IManhourService $manhourService) {
        $this->payrollService = $payrollService;
        $this->employeeService = $employeeService;
        $this->manhourService = $manhourService;
    }


    public function getPayroll($employeeId, $date) {

        $day = date_format($date, 'd');
        $monthYear  = date_format($date, 'Y-m');
        $endDate = 16;
        // Get proper date start
        if ($day <= 16) {
            $day = 1;
        }
        else {
            $day = 17;
            $endDate = date_format($date, 't');
        }

        $employee = $this->employeeService->getEmployeeById($employeeId);
        $rate = $employee->current['rate'];
        $payroll = new PayrollEntity();
        $gross = 0;

        for ($i = $day; $i <= $endDate; $i++) {
            // Create new date
            $date = date_create($monthYear.'-'.$day);
            $manhour = getSummaryOfRecord($employeeId, $date, $employee);

        }

    }
}
