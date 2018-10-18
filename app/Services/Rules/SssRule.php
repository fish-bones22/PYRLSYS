<?php

namespace App\Services;

use App\Contracts\RuleContracts\IRule;

class SssRule implements IRule {

    /// Get computed amount base on previous amount and period.
    public static function getAmount($baseAmount, $previousAmount, $isFirstPeriod) {

        if ($baseAmount == null)
            return 0;

        if (!$isFirstPeriod && $previousAmount != null) {
            $prevComputedAmount = getAmount($previousAmount, 0, true);
            $computedAmount = _getAmount($baseAmount + $previousAmount);

            return $computedAmount - $prevComputedAmount;
        }

        return _getAmount($baseAmount);

    }

    private function _getAmount($baseAmount) {

        $baseAmount = round($baseAmount, 2);
        $lowerLimit =   [1000,  1250,   1750,   2250,   2750,   3250,   3750,   4250,   4750,   5250,   5750,   6250,   6750,   7250,   7750,   8250,   8750,   9250,   9750,   10250,  10750,  11250,  11750,  12250,  12750,  13250,  13750,   14250,   14750,   15250,   15750];
        $value1 =       [36.30, 54.50,  72.70,  90.80,  109.0,  127.20, 145.30, 163.50, 181.70, 199.80, 218,    236.20, 254.30, 272.50, 290.70, 308.80, 327,    345.20, 363.30, 381.50, 399.70, 417.80, 436,    454.20, 472.30, 490.50, 508.70,  526.80,  545,     563.20,  581.30];
        $value2 =       [73.70, 110.50, 147.30, 184.20, 221.0,  257.80, 294.70, 331.50, 368.30, 405.20, 442.00, 478.80, 515.70, 552.50, 589.30, 626.20, 663.00, 699.80, 736.70, 773.50, 810.30, 847.20, 884.00, 920.80, 957.70, 994.50, 1031.30, 1068.20, 1105.00, 1141.80, 1178.70,];
        $limitInterval = 0.01;

        if ($baseAmount < $lowerLimit[0]) {
            return [0, 0];
        }

        for ($i = 0; $i < sizeof($lowerLimit); $i++) {

            if ($i == sizeof($lowerLimit) - 1
            || ($baseAmount > $lowerLimit[$i] && $baseAmount < $lowerLimit[$i + 1] - $limitInterval)) {
                return [ $value1[$i], $value2[$i] ];
            }

        }

        return [0, 0];

    }


}
