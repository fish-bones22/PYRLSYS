<?php

namespace App\Entities;

class ManhourSummaryEntity extends Entity {

    public $date;
    public $timeIn;
    public $timeOut;
    public $break;

    public $undertime;
    public $regularHours;
    public $otHours;
    public $totalHours;
    public $isHoliday;
    public $isExcused;
    public $totalPayableHours;

    public $rot;
    public $xot;
    public $sot;
    public $xsot;
    public $lhot;
    public $xlhot;
    public $nd;

    public $employee_id;
    public $employeeId;
    public $employeeName;
    public $timeCard;
    public $departmentId;
    public $departmentName;

    public $outlier;
    public $outlierId;
    public $authorized;
    public $remarks;

}
