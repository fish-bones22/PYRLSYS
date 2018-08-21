<?php

namespace App\Services;

use App\Contracts\IEmployeeService;
use App\Contracts\IManhourService;
use App\Contracts\IPayrollService;

use App\Entities\ManhourSummaryEntity;
use App\Entities\PayrollEntity;

class PayrollService implements IPayrollService {

    public $employeeService;
    public $manhourService;

    public function __construct (IEmployeeService $employeeService, IManhourService $manhourService) {
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
        if ($employee == null)
            return null;

        if ($employee->current['rate'] == null)
            return null;

        $rate = $employee->current['rate'];

        $payroll = new PayrollEntity();
        $gross = 0;
        $totalHours = 0;
        $workDays = 0;

        for ($i = $day; $i <= $endDate; $i++) {
            // Create new date
            $date = date_create($monthYear.'-'.$i);
            $manhour = $this->manhourService->getSummaryOfRecord($employeeId, $date, $employee);

            if ($manhour == null)
                continue;

            $hours = $manhour->regularHours != null ? $manhour->regularHours : 0;
            $totalHours += $hours;
            $gross += $hours * $rate;

        }

        $payroll->grossPay = $gross;
        $payroll->rate = $rate;
        $payroll->hours = $totalHours;

        return $payroll;

    }
}
