<?php

namespace App\Entities;

class EmployeeEntity extends Entity {

    public $id = 0;
    public $fullName;
    public $firstName;
    public $middleName;
    // Added Phone number 1 and 2
    public $phoneNumber1;
    public $phoneNumber2;
    public $maidenName;
    public $lastName;
    public $employeeId;
    public $sex;

    public $currentPicture;
    public $pictures;

    public $details = array();
    public $employmentDetails = array();
    public $deductibles = array();
    public $history = array();
    public $current = array();
    public $timeTable = array();
    public $timeTableHistory = array();
    public $payTable = array();
    public $payTableHistory = array();

    public $inactive = false;
    public $hasTin = false;

}
