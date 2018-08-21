<?php

namespace App\Entities;

class PayrollEntity extends Entity {

    public $regularHours;
    public $otHours;
    public $totalHours;

    public $basicPay;
    public $otPay;
    public $allowance;
    public $grossPay;

    public $workDays;

    public $exemption;
    public $taxableIncome;
    public $totalTaxableIncome;
    public $taxDueDate;
    public $remarks;

}
