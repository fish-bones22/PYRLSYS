<?php

namespace App\Contracts\RuleContracts;

interface IRule {

    public function getAmount($baseAmount, $previousAmount);

}
