<?php

namespace App\Services\Rules;

use App\Contracts\RuleContracts\IRule;

class WithholdingTaxRule implements IRule {

    /// Get computed amount base on previous amount and period.
    public static function getAmount($baseAmount, $previousAmount, $isFirstPeriod, $basis) {

        if ($baseAmount == null)
            return [0, 0];

        // if (!$isFirstPeriod && $previousAmount != null && $previousAmount != 0) {
        //     return [0, 0];
        // }

        return WithholdingTaxRule::_getAmount($baseAmount, $basis);

    }

    private static function _getAmount($baseAmount, $basis) {

        $baseAmount = round($baseAmount, 2);
        $compenstationLevel  =   [10417, 16667, 33333,     83333,      333333];
        $prescribedMinimum   =   [0,     1250,  5416.67,   20416.67,   100416.67];
        $rate                =   [0.20,  0.25,  0.30,      0.32,       0.35];

        if ($baseAmount <= $compenstationLevel[0]) {
            return [0, 0];
        }

        for ($i = 0; $i < sizeof($compenstationLevel); $i++) {
            if ($i == sizeof($compenstationLevel) - 1
            || ($baseAmount > $compenstationLevel[$i] && $baseAmount <= $compenstationLevel[$i + 1])
            )
            {
                $diff = $baseAmount - $compenstationLevel[$i];
                $tax = round($prescribedMinimum[$i] + ($diff * $rate[$i]), 2);
                return [ $tax, 0 ];
            }
        }

        return [0, 0];
    }


}
