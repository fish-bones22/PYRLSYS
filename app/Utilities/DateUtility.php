<?php

namespace App\Utilities;

class DateUtility {

    public static function getPreviousPeriod($date) {

        $day = date_format(date_create($date),'d');
        $year = date_format(date_create($date), 'Y');
        $month = date_format(date_create($date), 'm');

        $previousDate;

        if ($day < 16) {
            $strPrevMonth = $month != 1 ? $month - 1 : '12';
            $strPrevYear = $month != 1 ? $year : $year - 1;
            $strPrevDay = '16';
            $previousDate = date_create($strPrevYear.'-'.$strPrevMonth.'-'.$strPrevDay);
        }
        else {
            $strPrevDay = '01';
            $previousDate = date_create($year.'-'.$month.'-'.$strPrevDay);
        }

        return $previousDate;
    }

}
