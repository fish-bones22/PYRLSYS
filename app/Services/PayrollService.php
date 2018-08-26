<?php

namespace App\Services;

use App\Contracts\IEmployeeService;
use App\Contracts\IManhourService;
use App\Contracts\IPayrollService;
use App\Contracts\IDeductibleRecordService;
use App\Contracts\IAdjustmentsRecordService;

use App\Entities\ManhourSummaryEntity;
use App\Entities\PayrollEntity;

class PayrollService implements IPayrollService {

    public $employeeService;
    public $manhourService;
    public $deductibleRecordService;
    public $adjustmentsRecordService;

    public function __construct (IEmployeeService $employeeService, IManhourService $manhourService, IDeductibleRecordService $deductibleRecordService, IAdjustmentsRecordService $adjustmentsRecordService ) {
        $this->employeeService = $employeeService;
        $this->manhourService = $manhourService;
        $this->deductibleRecordService = $deductibleRecordService;
        $this->adjustmentsRecordService = $adjustmentsRecordService;
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
        $otDetails = array();
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

            //$totalAllowance += $allowance;

            $workDays++;

            // OT
            $otMultiplier = $this->getOtMultiplier($manhour);
            $otDetails = $this->getOtDetails($manhour, $otDetails, $rate);

            $otPay += ($otMultiplier['multiplier'] * $otMultiplier['value'] * $rate);
            $totalOtHours += $otMultiplier['value'];

        }

        $payroll->basicPay = $basicPay;
        $payroll->otPay = $otPay;
        $payroll->otDetails = $otDetails;
        $payroll->allowance = $totalAllowance;

        $payroll->grossPay = $basicPay + $otPay + $totalAllowance;

        $payroll->regularHours = $regularHours;
        $payroll->otHours = $totalOtHours;
        $payroll->totalHours = $totalOtHours + $regularHours;
        $payroll->workDays = $workDays;

        // Exemptions
        $summary = $this->getDeductibles($employeeId, $date);
        $payroll->exemptionDetails = $summary;
        $payroll->exemption = $summary['_TOTAL'];

        // Adjustments
        $summary = $this->getAdjustments($employeeId, $date);
        $payroll->adjustmentsDetails = $summary;
        $payroll->adjustments = $summary['_TOTAL'];

        // Net
        $payroll->netPay = $payroll->grossPay - $payroll->exemption;

        // Take home
        $payroll->takeHomePay = $payroll->netPay + $payroll->adjustments;

        return $payroll;

    }

    private function getOtMultiplier($manhour) {

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
            'value' => 0
        ];
    }

    private function getOtDetails($model, $details, $rate) {
        // Hours
        if (!isset($details['rot']))
            $details['rot'] = 0;
        if (!isset($details['sot']))
            $details['sot'] = 0;
        if (!isset($details['xsot']))
            $details['xsot'] = 0;
        if (!isset($details['lhot']))
            $details['lhot'] = 0;
        if (!isset($details['xlhot']))
            $details['xlhot'] = 0;
        if (!isset($details['nd']))
            $details['nd'] = 0;
        // Amount
        if (!isset($details['rotrate']))
            $details['rotrate'] = 0;
        if (!isset($details['sotrate']))
            $details['sotrate'] = 0;
        if (!isset($details['xsotrate']))
            $details['xsotrate'] = 0;
        if (!isset($details['lhotrate']))
            $details['lhotrate'] = 0;
        if (!isset($details['xlhotrate']))
            $details['xlhotrate'] = 0;
        if (!isset($details['ndrate']))
            $details['ndrate'] = 0;

        $details['rot']  += ($model->rot != null ? $model->rot : 0);
        $details['sot']  += ($model->sot != null ? $model->sot : 0);
        $details['xsot']  += ($model->xsot != null ? $model->xsot : 0);
        $details['lhot'] += ($model->lhot != null ? $model->lhot : 0);
        $details['xlhot'] += ($model->xlhot != null ? $model->xlhot : 0);
        $details['nd'] += ($model->xlhot != null ? $model->xlhot : 0);

        $details['rotrate']  += ($model->rot != null ? $model->rot*$rate*1.25 : 0);
        $details['sotrate']  += ($model->sot != null ? $model->sot*$rate*1.3 : 0);
        $details['xsotrate']  += ($model->xsot != null ? $model->xsot*$rate*1.69 : 0);
        $details['lhotrate'] += ($model->lhot != null ? $model->lhot*$rate*2 : 0);
        $details['xlhotrate'] += ($model->xlhot != null ? $model->xlhot*$rate*2.69 : 0);
        $details['ndrate'] += ($model->xlhot != null ? $model->xlhot*$rate*0.1 : 0);
        return $details;
    }

    private function getDeductibles($employeeId, $date) {

        $monthYear = date_format($date, 'Y-m');
        $startDay = date_format($date, 'd') <= 16 ? '01' : '17';

        $records = $this->deductibleRecordService->getEmployeeDeductiblesOnDate($employeeId, $monthYear.'-'.$startDay);
        $summary = array();
        $total = 0;
        foreach ($records as $key => $record) {
            $summary[$record->details] = $record->amount;
            $total += $record->amount;
        }

        $summary['_TOTAL'] = $total;

        return $summary;
    }

    private function getAdjustments($employeeId, $date) {

        $monthYear = date_format($date, 'Y-m');
        $startDay = date_format($date, 'd') <= 16 ? '01' : '17';

        $records = $this->adjustmentsRecordService->getEmployeeAdjustmentsOnDate($employeeId, $monthYear.'-'.$startDay);
        $summary = array();
        $total = 0;
        foreach ($records as $key => $record) {
            $summary[$key] = $record->amount;
            $total += $record->amount;
        }

        $summary['_TOTAL'] = $total;

        return $summary;
    }
}
