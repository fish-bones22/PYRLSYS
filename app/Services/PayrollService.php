<?php

namespace App\Services;

use App\Contracts\IEmployeeService;
use App\Contracts\IManhourService;
use App\Contracts\IPayrollService;
use App\Contracts\IDeductibleRecordService;
use App\Contracts\IAdjustmentsRecordService;

use App\Entities\ManhourSummaryEntity;
use App\Entities\PayrollEntity;

use App\Services\Rules\SssRule;
use App\Services\Rules\PhilhealthRule;
use App\Services\Rules\PagibigRule;
use App\Services\Rules\WithholdingTaxRule;
use App\Utilities\DateUtility;

class PayrollService implements IPayrollService {

    public $employeeService;
    public $manhourService;
    public $deductibleRecordService;
    public $adjustmentsRecordService;

    private $hoursPerDay = 8;
    private $workDays = 13;

    public function __construct (IEmployeeService $employeeService, IManhourService $manhourService, IDeductibleRecordService $deductibleRecordService, IAdjustmentsRecordService $adjustmentsRecordService ) {
        $this->employeeService = $employeeService;
        $this->manhourService = $manhourService;
        $this->deductibleRecordService = $deductibleRecordService;
        $this->adjustmentsRecordService = $adjustmentsRecordService;
    }


    public function getPayroll($employeeId, $date) {

        $year = date_format($date, 'Y');
        $month = date_format($date, 'm');

        $day = date_format($date, 'd');
        $monthYear  = date_format($date, 'Y-m');
        $endDate = 15;

        $daysOfMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $daysOfPeriod = 15;

        // Get proper date start
        if ($day <= 15) {
            $day = 1;
        }
        else {
            $day = 16;
            $endDate = date_format($date, 't');
            $daysOfPeriod = $daysOfMonth - 15;
        }

        $sundays = $this->countSundays(date_create($monthYear.'-'.$day), date_create($monthYear.'-'.$endDate));
        $this->workDays = $daysOfPeriod - $sundays;

        $payroll = $this->getBasicPay($employeeId, $date);

        if ($payroll == null)
            return null;

        // Exemptions
        $summary = $this->getDeductibles($employeeId, $date);
        $payroll->exemptionDetails = $summary;
        $payroll->exemption = $summary['_TOTAL'];

        // Net before tax
        $payroll->beforeTaxPay = $payroll->grossPay;

        // Net
        $payroll->netPay = $payroll->grossPay - $payroll->exemption;
        if ($payroll->fixed) {
            $payroll->adjFixed = $payroll->netPay;
        }

        // Take home
        $payroll->takeHomePay = round($payroll->netPay + $payroll->adjustments + $payroll->otherAdjustments + $payroll->allowance, 2);
        // No negative result
        $payroll->takeHomePay = $payroll->takeHomePay > 0 ? $payroll->takeHomePay : 0;

        return $payroll;

    }


    public function getBasicPay($employeeId, $date) {


        $year = date_format($date, 'Y');
        $month = date_format($date, 'm');

        $day = date_format($date, 'd');
        $monthYear  = date_format($date, 'Y-m');
        $endDate = 15;

        $daysOfMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $daysOfPeriod = 15;

        // Get proper date start
        if ($day <= 15) {
            $day = 1;
        }
        else {
            $day = 16;
            $endDate = date_format($date, 't');
            $daysOfPeriod = $daysOfMonth - 15;
        }

        $sundays = $this->countSundays(date_create($monthYear.'-'.$day), date_create($monthYear.'-'.$endDate));
        $this->workDays = $daysOfPeriod - $sundays;

        $employee = $this->employeeService->getEmployeeById($employeeId);
        if ($employee == null)
            return null;

        $payroll = new PayrollEntity();

        $payroll->employeeRemittances = $employee->deductibles;

        $payroll->employeeId = $employee->employeeId;
        $payroll->employeeName = $employee->fullName;
        $payroll->employeeDepartment = $employee->current['department']['displayName'];
        $payroll->dateStart = $monthYear.'-'.$day ;
        $payroll->dateEnd = $monthYear.'-'.$endDate ;
        $payroll->period =  date_format($date, 'M').' '.$day.'-'.$endDate.', '.date_format($date, 'Y');
        $payroll->details = array();

        $basicPay = 0;
        $otPay = 0;
        $rotPay = 0;
        $xotPay = 0;
        $sotPay = 0;
        $xsotPay = 0;
        $lhotPay = 0;
        $xlhotPay = 0;
        $ndPay = 0;
        $totalAllowance = 0;
        $regularHours = 0;
        $totalOtHours = 0;
        $workingDays = 0;
        $hourlyRate = 0;
        $otDetails = array();
        $prevId = 0;
        $hasFixed = false;
        $fixedAllowance = 0;
        $fixedRate = 0;

        $payTable = array();

        for ($i = $day; $i <= $endDate; $i++) {
            // Create new date
            $datestr = $monthYear.'-'.$i;
            $date = date_create($monthYear.'-'.$i);
            $manhour = $this->manhourService->getSummaryOfRecord($employeeId, $datestr, $employee);
            if ($manhour == null || $manhour->date == null) {
                continue;
            }

            $payRecord = $this->employeeService->getEmployeePayTable($employeeId, $date);
            $timeTable = $this->employeeService->getEmployeeTimeTable($employeeId, $date);
            $holiday = $this->manhourService->getHoliday($monthYear.'-'.$i);

            if (isset($payRecord['id']) && $prevId !== $payRecord['id']) {
                if (sizeof($payroll->details) <= 0) {
                    $payRecord['startdate'] = $monthYear.'-'.$i;
                }
                $payroll->details[] = $payRecord;
                $prevId = $payRecord['id'];
            }

            $rateBasis = 'monthly';
            $rate = 0;
            $allowance = 0;
            $hourlyAllowance = 0;
            $break = isset($timeTable['break']) && $timeTable['break'] != null ? $timeTable['break'] : 0;

            if ($payRecord['rate'] != null)
                $rate = $payRecord['rate'];

            if ($payRecord['ratebasis'] != null)
                $rateBasis = $payRecord['ratebasis'];

            if (isset($payRecord['allowance']) && $payRecord['allowance'] != null)
                $allowance = $payRecord['allowance'];

            if ($rateBasis == 'daily') {
                $hourlyRate = $rate/$this->hoursPerDay;
                $hourlyAllowance = $allowance/$this->hoursPerDay;
            }
            else if ($rateBasis == 'monthly') {
                $hourlyRate = ($rate/2)/($this->workDays*$this->hoursPerDay);
                $hourlyAllowance = ($allowance/2)/($this->workDays*$this->hoursPerDay);
                $allowance = ($allowance/2)/$this->workDays;
            }
            else if ($rateBasis == 'fixed') {
                $fixedRate = $rate;
                $fixedAllowance = $allowance;
                $hasFixed = true;
            }

            // Is holiday and has manhour record
            if ($manhour->regularHours != null && $manhour->regularHours > 0) {
                if ($holiday != null && $holiday['type'] == 'legal') {
                    $hourlyRate *= 2;
                } else if ($holiday != null && $holiday['type'] == 'special') {
                    $hourlyRate *= 1.3;
                }
            }

            // Actual hours accounts for the total hours the employee actually logged
            $actualHours = $manhour->regularHours != null ? $manhour->regularHours : 0;
            // Hours account for all payable hours the employee have
            $hours = $manhour->totalPayableHours != null ? $manhour->totalPayableHours : 0;
            $regularHours += $hours;
            $basicPay += $hours * $hourlyRate;

            $totalAllowance += $hourlyAllowance * $hours;

            $workingDays++;


            // OT
            $otRate = $hourlyRate + $hourlyAllowance;
            $otMultipliers = $this->getOtMultiplier($manhour);
            $otDetails = $this->getOtDetails($manhour, $otDetails, $otRate);

            foreach ($otMultipliers as $key => $otMultiplier) {
                if ($key === 'rot') {
                    $rotPay += ($otMultiplier['multiplier'] * $otMultiplier['value'] * $otRate);
                }
                else if ($key === 'xot') {
                    $xotPay += ($otMultiplier['multiplier'] * $otMultiplier['value'] * $otRate);
                }
                else if ($key === 'sot') {
                    $sotPay += ($otMultiplier['multiplier'] * $otMultiplier['value'] * $otRate);
                    // Add allowance for SOT
                    $totalAllowance += $hourlyAllowance * $otMultiplier['value'];
                }
                else if ($key === 'xsot') {
                    $xsotPay += ($otMultiplier['multiplier'] * $otMultiplier['value'] * $otRate);
                }
                else if ($key === 'lhot') {
                    $lhotPay += ($otMultiplier['multiplier'] * $otMultiplier['value'] * $otRate);
                }
                else if ($key === 'xlhot') {
                    $xlhotPay += ($otMultiplier['multiplier'] * $otMultiplier['value'] * $otRate);
                }
                if ($key === 'nd') {
                    $ndPay += ($otMultiplier['multiplier'] * $otMultiplier['value'] * $otRate);
                }
            }

            $otPay += ($otMultiplier['multiplier'] * $otMultiplier['value'] * $otRate);
            $totalOtHours += $otMultiplier['value'];

        }

        // Basic adjustments
        $summary = $this->getAdjustments($employeeId, $date);
        $basicAdj = isset($summary['basicadjustment']) ? $summary['basicadjustment'] : 0;
        $otAdj = isset($summary['overtimeadjustment']) ? $summary['overtimeadjustment'] : 0;

        $monRate = $this->getComputedMonthlyRate($employeeId, $date)/2;
        $basicPay = $basicPay > $monRate ? $monRate : $basicPay;
        $payroll->basicPayBase = round($basicPay, 2);
        $basicPay += $basicAdj;

        $payroll->otherAdjustments = $summary['_OTHER_ADJUSTMENTS'];
        $payroll->adjustmentsDetails = $summary;

        $payroll->hourlyRate = $hourlyRate;
        $payroll->basicPay = round($basicPay, 2);
        $payroll->otPay = round($otPay, 2);
        $payroll->rotPay = round($rotPay, 2);
        $payroll->xotPay = round($xotPay, 2);
        $payroll->sotPay = round($sotPay, 2);
        $payroll->xsotPay = round($xsotPay, 2);
        $payroll->lhotPay = round($lhotPay, 2);
        $payroll->xlhotPay = round($xlhotPay, 2);
        $payroll->ndPay = round($ndPay, 2);
        $payroll->otDetails = $otDetails;
        $payroll->allowance = round($totalAllowance, 2);

        $payroll->fixed = $hasFixed;
        // Exception of Fixed rate basis
        if ($hasFixed) {
            $pay = $fixedRate / 2;
            $payroll->basicPayBase = round($pay, 2);
            $payroll->basicPayFixed = $basicPay;
            $basicPay = $pay + $basicAdj;
            $totalAllowance = $fixedAllowance;
            $payroll->hourlyRate = 0;
            $payroll->basicPay = round($basicPay, 2);
            $payroll->otPay = 0;
            $payroll->rotPay = 0;
            $payroll->ndPay = 0;
            $payroll->otDetails = null;
            $payroll->allowance = round($totalAllowance, 2);
        }

        $payroll->grossPay = round($basicPay + $otPay + $otAdj, 2);

        $payroll->regularHours = $regularHours;
        $payroll->otHours = $totalOtHours;
        $payroll->totalHours = $totalOtHours + $regularHours;
        $payroll->workDays = $workingDays;

        return $payroll;

    }


    public function getComputedMonthlyRate($employeeId, $date) {

        $payRecord = $this->employeeService->getEmployeePayTable($employeeId, $date);
        $workDays = $this->workDays * 2;

        if ($payRecord == null)
            return 0;

        $rateBasis = 'monthly';
        $rate = 0;
        $monthlyRate = 0;

        if ($payRecord['rate'] != null)
            $rate = $payRecord['rate'];

        if ($payRecord['ratebasis'] != null)
            $rateBasis = $payRecord['ratebasis'];

        if ($rateBasis == 'daily') {
            // Adjust rate if holiday
            $holiday = $this->manhourService->getHoliday(date_format($date, 'Y-m-d'));
            if ($holiday != null && $holiday['type'] == 'legal') {
                $rate *= 2;
            } else if ($holiday != null && $holiday['type'] == 'special') {
                $rate *= 1.3;
            }

            $monthlyRate = $rate * $workDays;
        }
        else {
            $monthlyRate = $rate;
        }

        return $monthlyRate;

    }


    public function getRemittanceDeductible($employeeId, $date) {

        $previousDate = DateUtility::getPreviousPeriod($date);

        $isFirstPeriod = false;

        if (date_format($previousDate, 'd') < 16) {
            $isFirstPeriod = true;
        }


        $rate = $this->getComputedMonthlyRate($employeeId, date_create($date));
        $previousRate = $this->getComputedMonthlyRate($employeeId, $previousDate);

        // If no basic pay (new hire etc..)
        $previousBasicPay = $this->getPayroll($employeeId, $previousDate);

        if ($previousBasicPay == null || $previousBasicPay->basicPay <= 0) {
            $isFirstPeriod = true;
        }

        $currentBasicPay = $this->getPayroll($employeeId, date_create($date));
        $basis = $currentBasicPay->rateBasis;


        $taxablePay = 0;
        if ($basis === 'fixed') {
            $taxablePay = $rate/2;
        } else {
            $taxablePay = $currentBasicPay != null ? $currentBasicPay->grossPay : 0;
        }

        $value = array();

        if (isset($currentBasicPay->employeeRemittances['sss'])) {
            $sssRemmittance = 0;
            if ($basis === 'fixed') {
                $sssRemmittance = SssRule::getAmount($rate, $previousRate, $isFirstPeriod, $basis);
            } else {
                $sssRemmittance = SssRule::getAmount($currentBasicPay != null ? $currentBasicPay->basicPay : 0, $previousBasicPay != null ? $previousBasicPay->basicPay : 0, $isFirstPeriod, $basis);
            }
            $value['sss'] = $sssRemmittance;
            $taxablePay -= $sssRemmittance[0];
        }
        if (isset($currentBasicPay->employeeRemittances['philhealth'])) {
            $philhealthRemittance = PhilhealthRule::getAmount($rate, $previousRate, $isFirstPeriod, $basis);
            $value['philhealth'] = $philhealthRemittance;
            $taxablePay -= $philhealthRemittance[0];
        }
        if (isset($currentBasicPay->employeeRemittances['pagibig'])) {
            $pagibigRemittance = PagibigRule::getAmount($rate, $previousRate, $isFirstPeriod, $basis);
            $value['pagibig'] = $pagibigRemittance;
            $taxablePay -= $pagibigRemittance[0];
        }
        if (isset($currentBasicPay->employeeRemittances['tin'])) {
            $withholdingTax = WithholdingTaxRule::getAmount($taxablePay, 0, $isFirstPeriod, $basis);
            $value['tin'] = $withholdingTax;
        }

        return $value;
    }

    private function getOtMultiplier($manhour) {

        $otDetails = array();

        if ($manhour->rot != '') {
            $otDetails['rot'] = [
                'key' => 'rot',
                'multiplier' => 1.25,
                'value' => $manhour->rot
            ];
        }

        if ($manhour->xot != '') {
            $otDetails['xot'] =  [
                'key' => 'xot',
                'multiplier' => 1.25,
                'value' => $manhour->xot
            ];
        }

        if ($manhour->sot != ''){
            $otDetails['sot'] = [
                'key' => 'sot',
                'multiplier' => 1.3,
                'value' => $manhour->sot
            ];
        }

        if ($manhour->xsot != ''){
            $otDetails['xsot'] = [
                'key' => 'xsot',
                'multiplier' => 1.69,
                'value' => $manhour->xsot
            ];
        }

        if ($manhour->lhot != ''){
            $otDetails['lhot'] = [
                'key' => 'lhot',
                'multiplier' => 2,
                'value' => $manhour->lhot
            ];
        }

        if ($manhour->xlhot != ''){
            $otDetails['xlhot'] = [
                'key' => 'xlhot',
                'multiplier' => 2.69,
                'value' => $manhour->xlhot
            ];
        }

        if ($manhour->nd != ''){
            $otDetails['nd'] = [
                'key' => 'nd',
                'multiplier' => 0.1,
                'value' => $manhour->nd
            ];
        }

        if (sizeof($otDetails) > 0)
            return $otDetails;

         return ['no' => [
                'key' => 'no',
                'multiplier' => 1,
                'value' => 0
            ]];
    }

    private function getOtDetails($model, $details, $rate) {
        // Hours
        if (!isset($details['rot']))
            $details['rot'] = 0;
        if (!isset($details['xot']))
            $details['xot'] = 0;
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
        if (!isset($details['xotrate']))
            $details['xotrate'] = 0;
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
        $details['xot']  += ($model->xot != null ? $model->xot : 0);
        $details['sot']  += ($model->sot != null ? $model->sot : 0);
        $details['xsot']  += ($model->xsot != null ? $model->xsot : 0);
        $details['lhot'] += ($model->lhot != null ? $model->lhot : 0);
        $details['xlhot'] += ($model->xlhot != null ? $model->xlhot : 0);
        $details['nd'] += ($model->nd != null ? $model->nd : 0);

        $details['rotrate']  += ($model->rot != null ? round($model->rot*$rate*1.25, 2) : 0);
        $details['xotrate']  += ($model->xot != null ? round($model->xot*$rate*1.25, 2) : 0);
        $details['sotrate']  += ($model->sot != null ? round($model->sot*$rate*1.3, 2) : 0);
        $details['xsotrate']  += ($model->xsot != null ? round($model->xsot*$rate*1.69, 2) : 0);
        $details['lhotrate'] += ($model->lhot != null ? round($model->lhot*$rate*2, 2) : 0);
        $details['xlhotrate'] += ($model->xlhot != null ? round($model->xlhot*$rate*2.69, 2) : 0);
        $details['ndrate'] += ($model->nd != null ? round($model->nd*$rate*0.1, 2) : 0);
        return $details;
    }

    private function getDeductibles($employeeId, $date) {

        $monthYear = date_format($date, 'Y-m');
        $startDay = date_format($date, 'd') <= 15 ? '01' : '16';

        $records = $this->deductibleRecordService->getEmployeeDeductiblesOnDate($employeeId, $monthYear.'-'.$startDay);
        $summary = array();
        $total = 0;
        foreach ($records as $key => $record) {
            $summary[$record->details] = $record->amount;
            $total += $record->amount;
        }

        $summary['_TOTAL_BEFORE_TAX'] = $total - (isset($summary['tin']) ? $summary['tin'] : 0);
        $summary['_TOTAL'] = $total;

        return $summary;
    }

    private function getAdjustments($employeeId, $date) {

        $monthYear = date_format($date, 'Y-m');
        $startDay = date_format($date, 'd') <= 15 ? '01' : '16';

        $records = $this->adjustmentsRecordService->getEmployeeAdjustmentsOnDate($employeeId, $monthYear.'-'.$startDay);
        $summary = array();
        $total = 0;
        $otherAdjustments = 0;
        foreach ($records as $key => $record) {
            $summary[$record->details] = $record->amount;
            $total += $record->amount;

            if ($key == 'basicadjustment' || $key == 'overtimeadjustment') {
                continue;
            }

            $otherAdjustments += $record->amount;

        }
        $summary['_OTHER_ADJUSTMENTS'] = $otherAdjustments;
        $summary['_TOTAL'] = $total;

        return $summary;
    }


    private function countSundays($startDate, $endDate) {

        $days = $startDate->diff($endDate, true)->days;
        $sundays = floor($days/7) + ($startDate->format('N') + $days % 7 >= 7);

        return $sundays;

    }
}
