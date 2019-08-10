<?php

namespace App\Contracts\RuleContracts;

interface IRule {

    public static function getAmount($baseAmount, $previousAmount, $isFirstPeriod, $basis);

}
