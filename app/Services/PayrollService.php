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


        $payroll = new PayrollEntity();
        $basicPay = 0;
        $otPay = 0;
        $totalAllowance = 0;
        $regularHours = 0;
        $totalOtHours = 0;
        $workDays = 0;

        for ($i = $day; $i <= $endDate; $i++) {
            // Create new date
            $date = date_create($monthYear.'-'.$i);
            $manhour = $this->manhourService->getSummaryOfRecord($employeeId, $date, $employee);

            if ($manhour == null || $manhour->date == null)
                continue;

            $history = $this->employeeService->getEmployeeHistoryOnDate($employeeId, $date);

            $rate = 0;
            $allowance = 0;
            if ($history['rate'] != null)
                $rate = $history['rate'];
            if ($history['allowance'] != null)
                $allowance = $history['allowance'];

            $hours = $manhour->regularHours != null ? $manhour->regularHours : 0;
            $regularHours += $hours;
            $basicPay += $hours * $rate;

            $totalAllowance += $allowance;

            $workDays++;

            // OT
            $otDetails = $this->getOtDetails($manhour);
            $otPay += ($otDetails['multiplier'] * $otDetails['value'] * $rate);
            $totalOtHours += $otDetails['value'];

        }

        $payroll->basicPay = $basicPay;
        $payroll->otPay = $otPay;
        $payroll->allowance = $totalAllowance;

        $payroll->grossPay = $basicPay + $otPay + $totalAllowance;

        $payroll->regularHours = $regularHours;
        $payroll->otHours = $totalOtHours;
        $payroll->totalHours = $totalOtHours + $regularHours;
        $payroll->workDays = $workDays;

        return $payroll;

    }

    private function getOtDetails($manhour) {

        $otDetails = array();

        if ($manhour->rot != '') {
            return [
                'multiplier' => 1.25,
                'value' => $manhour->rot
            ];
        }

        if ($manhour->sot != ''){
            return [
                'multiplier' => 1.3,
                'value' => $manhour->sot
            ];
        }

        if ($manhour->xsot != ''){
            return [
                'multiplier' => 1.69,
                'value' => $manhour->xsot
            ];
        }

        if ($manhour->lhot != ''){
            return [
                'multiplier' => 2,
                'value' => $manhour->lhot
            ];
        }

        if ($manhour->xlhot != ''){
            return [
                'multiplier' => 2.69,
                'value' => $manhour->xlhot
            ];
        }

        return [
            'multiplier' => 1,
            'value' => 1
        ];
    }
}
