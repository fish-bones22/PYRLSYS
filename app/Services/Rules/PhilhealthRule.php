<?php

namespace App\Services\Rules;

use App\Contracts\RuleContracts\IRule;

class PhilhealthRule implements IRule {

    /// Get computed amount base on previous amount and period.
    public static function getAmount($baseAmount, $previousAmount, $isFirstPeriod, $basis) {

        if ($baseAmount == null)
            return [0, 0];

        if (!$isFirstPeriod && $previousAmount != null && $previousAmount != 0) {
            return [0, 0];
        }

        return PhilhealthRule::_getAmount($baseAmount, $basis);

    }

    private static function _getAmount($baseAmount, $basis) {

        $baseAmount = round($baseAmount, 2);
        $lowerLimit =   [0,     9000,  10000,   11000,   12000,   13000,   14000,   15000,   16000,   17000,   18000,   19000,   20000,   21000,   22000,   23000,   24000,   25000,   26000,   27000,   28000,  29000,  30000,  31000,  32000,  33000,  34000,  35000];
        $value1 =       [100,   112.5,   125,     137.5,   150,     162.5,   175,     187.5,   200,     212.5,   225,     237.5,   250,     262.5,   275,     287.5,   300,     312.5,   325,     337.5,  350,    362.5,  375,    387.5,  400,    412.5,  425,   437.5];

        $limitInterval = 0.01;

        if ($baseAmount < $lowerLimit[0]) {
            return [0, 0];
        }

        for ($i = 0; $i < sizeof($lowerLimit); $i++) {

            if ($i == sizeof($lowerLimit) - 1
            || ($baseAmount > $lowerLimit[$i] && $baseAmount < $lowerLimit[$i + 1] - $limitInterval)) {
                return [ $value1[$i], $value1[$i] ];
            }

        }

        return [0, 0];

    }


}
