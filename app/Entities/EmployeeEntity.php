<?php

namespace App\Entities;

class EmployeeEntity extends Entity {

    public $id = 0;
    public $fullName;
    public $firstName;
    public $middleName;
    public $lastName;
    public $employeeId;
    public $sex;

    public $currentPicture;
    public $pictures;

    public $details;
    public $employmentDetails;

    public $contactNumber;
    public $email;
    public $otherContacts;

}
