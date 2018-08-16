<?php

namespace App\Entities;

class EmployeeEntity extends Entity {

    public $id = 0;
    public $fullName;
    public $firstName;
    public $middleName;
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

}
