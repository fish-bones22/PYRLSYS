<?php

namespace App\Services\Rules;

use App\Contracts\RuleContracts\IRule;

class PagibigRule implements IRule {

    /// Get computed amount base on previous amount and period.
    public static function getAmount($baseAmount, $previousAmount, $isFirstPeriod, $basis) {

        if ($baseAmount == null)
            return [0, 0];

        if (!$isFirstPeriod && $previousAmount != null && $previousAmount != 0) {
            return [0, 0];
        }

        return PagibigRule::_getAmount($baseAmount, $basis);

    }

    private static function _getAmount($baseAmount, $basis) {

        $baseAmount = round($baseAmount, 2);

        $percentage1 = 0.01;
        $percentage2 = 0.02;
        $maxAmount = 100;
        return [$maxAmount, $maxAmount];

        // if ($baseAmount > 1500) {
        //     $percentage1 = 0.01;
        // }

        // if ($baseAmount > 5000) {
        //     return [$maxAmount, $maxAmount];
        // }

        // return [round($baseAmount * $percentage2, 2), round($baseAmount * $percentage1, 2)];

    }


}
