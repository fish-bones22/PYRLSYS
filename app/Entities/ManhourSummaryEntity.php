<?php

namespace App\Entities;

class ManhourSummaryEntity extends Entity {

    public $date;
    public $timeIn;
    public $timeOut;
    public $undertime;
    public $hours;

    public $rot;
    public $sot;
    public $xsot;
    public $lhot;
    public $xlhot;

    public $employee_id;
    public $employeeId;
    public $employeeName;
    public $timeCard;
    public $departmentId;
    public $departmentName;

    public $outlier;
    public $authorized;
    public $remarks;

}
