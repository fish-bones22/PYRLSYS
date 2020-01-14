<?php

namespace App\Services\Rules;

use App\Contracts\RuleContracts\IRule;

class PhilhealthRule implements IRule {

    /// Get computed amount base on previous amount and period.
    public static function getAmount($baseAmount, $previousAmount, $isFirstPeriod, $basis, $date = null) {

        if ($baseAmount == null)
            return [0, 0];

        // if (!$isFirstPeriod && $previousAmount != null && $previousAmount != 0) {
        //     return [0, 0];
        // }

        return PhilhealthRule::_getAmount($baseAmount, $basis, $date);

    }

    private static function _getAmount($baseAmount, $basis, $date = null) {

        $baseAmount = round($baseAmount, 2);

        $minimumAmount = 10000;
        $maximumAmount = 40000;
        $factor = 0.0275;

        // Adjust by year
        if ($date !== null) {
            $year = date_create($date)->format('Y')*1;

            if ($year === 2020) {
                $minimumAmount = 10000;
                $maximumAmount = 60000;
                $factor = 0.03;
            } else if ($year === 2021) {
                $minimumAmount = 10000;
                $maximumAmount = 70000;
                $factor = 0.035;
            } else if ($year === 2022) {
                $minimumAmount = 10000;
                $maximumAmount = 80000;
                $factor = 0.04;
            } else if ($year === 2023) {
                $minimumAmount = 10000;
                $maximumAmount = 90000;
                $factor = 0.045;
            } else if ($year >= 2024) {
                $minimumAmount = 10000;
                $maximumAmount = 100000;
                $factor = 0.05;
            }
        }

        if ($baseAmount <= $minimumAmount) {
            $baseAmount = $minimumAmount;
        }

        if ($baseAmount >= $maximumAmount) {
            $baseAmount = $maximumAmount;
        }

        $amount = round($baseAmount * $factor, 2)/2;
        return [round($amount/2, 2), round($amount/2, 2)];
    }


}
