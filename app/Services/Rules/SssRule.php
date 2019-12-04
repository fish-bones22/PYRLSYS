<?php

namespace App\Services\Rules;

use App\Contracts\RuleContracts\IRule;

class SssRule implements IRule {

    /// Get computed amount base on previous amount and period.
    public static function getAmount($baseAmount, $previousAmount, $isFirstPeriod, $basis) {

        if ($baseAmount == null)
            return [0, 0, 0];

        if (!$isFirstPeriod && $previousAmount != null) {
            $prevComputedAmount = SssRule::getAmount($previousAmount, 0, true, $basis);
            $computedAmount = SssRule::_getAmount($baseAmount + $previousAmount, $basis);

            return [$computedAmount[0] - $prevComputedAmount[0], $computedAmount[1] - $prevComputedAmount[1], $computedAmount[2] - $prevComputedAmount[2]];
        }

        return SssRule::_getAmount($baseAmount, $basis);

    }

    private static function _getAmount($baseAmount, $basis) {

        $baseAmount = round($baseAmount, 2);
        $lowerLimit =   [2250,  2750,   3250,   3750,   4250,   4750,   5250,   5750,   6250,   6750,   7250,   7750,   8250,   8750,   9250,   9750,   10250,  10750,  11250,  11750,  12250,  12750,  13250,  13750,  14250,  14750,  15250,   15750,   16250.,  16750,   17250,   17750,   18250,   18750,   19250,  19750 ];
        $value1 =       [100,   120,    140,    160,    180,    200,    220,    240,    260,    280,    300,    320,    340,    360,    380,    400,    420,    440,    460,    480,    500,    520,    540,    560,    580,    600,    620,     640,      660,    680,     700,     720,     740,     760,     780,    800   ];
        $value2 =       [200,   240,    280,    320,    360,    400,    440,    480,    520,    560,    600,    640,    680,    720,    760,    800,    840,    880,    920,    960,    1000,   1040,   1080,   1120,   1160,   1200,   1240,    1280,     1320,   1360,    1400,    1440,    1480,    1520,    1560,   1600  ];
        $value3 =       [10,    10,     10,     10,     10,     10,     10,     10,     10,     10,     10,     10,     10,     10,     10,     10,     10,     10,     10,     10,     10,     10,     10,     10,     10,     10,     10,      10,      30,      30,      30];
        $limitInterval = 0.01;

        if ($baseAmount < $lowerLimit[0]) {
            return [0, 0, 0];
        }

        for ($i = 0; $i < sizeof($lowerLimit); $i++) {

            if ($i == sizeof($lowerLimit) - 1
            || ($baseAmount > $lowerLimit[$i] && $baseAmount < $lowerLimit[$i + 1] - $limitInterval)) {
                return [ $value1[$i], $value2[$i], $value3[$i] ];
            }

        }

        return [0, 0, 0];

    }


}
