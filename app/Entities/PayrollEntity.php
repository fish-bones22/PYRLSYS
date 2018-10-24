<?php

namespace App\Entities;

class PayrollEntity extends Entity {

    public $employeeId;
    public $employeeName;
    public $employeeDepartment;
    public $dateStart;
    public $dateEnd;
    public $period;

    public $employeeRemittances;

    public $modeOfPayment;
    public $rate;
    public $rateBasis;
    public $hourlyRate;
    public $regularHours;
    public $otHours;
    public $rotHours;
    public $ndHours;
    public $totalHours;

    public $basicPay;
    public $otPay;
    public $rotPay;
    public $ndPay;
    public $allowance;
    public $grossPay;
    public $netPay;
    public $beforeTaxPay;
    public $takeHomePay;

    public $otDetails;

    public $workDays;

    public $exemption;
    public $exemptionDetails;

    public $adjustments;
    public $adjustmentsDetails;

    public $taxableIncome;
    public $totalTaxableIncome;

    public $taxDueDate;
    public $remarks;

}
