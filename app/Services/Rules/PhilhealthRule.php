<?php

namespace App\Services\Rules;

use App\Contracts\RuleContracts\IRule;

class PhilhealthRule implements IRule {

    /// Get computed amount base on previous amount and period.
    public static function getAmount($baseAmount, $previousAmount, $isFirstPeriod, $basis) {

        if ($baseAmount == null)
            return [0, 0];

        // if (!$isFirstPeriod && $previousAmount != null && $previousAmount != 0) {
        //     return [0, 0];
        // }

        return PhilhealthRule::_getAmount($baseAmount, $basis);

    }

    private static function _getAmount($baseAmount, $basis) {

        $baseAmount = round($baseAmount, 2);

        $minimumAmount = 10000;
        $maximumAmount = 40000;
        $minPremium = 137.50;
        $maxPremium = 550;
        $factor = 0.0275;

        if ($baseAmount <= $minimumAmount) {
            return [$minPremium, $minPremium];
        }

        if ($baseAmount >= $maximumAmount) {
            return [$maxPremium, $maxPremium];
        }

        $amount = round($baseAmount * $factor, 2)/2;
        return [round($amount/2, 2), round($amount/2, 2)];
    }


}
