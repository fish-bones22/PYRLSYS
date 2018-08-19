<?php

namespace App\Entities;

class PayrollEntity extends Entity {

    public $grossPay;
    public $exemption;
    public $taxableIncome;
    public $allowance;
    public $totalTaxableIncome;
    public $taxDueDate;
    public $remarks;

}
