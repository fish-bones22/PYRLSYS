<?php

namespace App\Entities;

class PayrollEntity extends Entity {

    public $rate;
    public $regularHours;
    public $otHours;
    public $totalHours;

    public $basicPay;
    public $otPay;
    public $allowance;
    public $grossPay;
    public $netPay;
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
