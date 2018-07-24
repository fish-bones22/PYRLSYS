<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Model\EmployeeDetail;
use App\Model\EmployeePicture;

class Employee extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employeeId', 'firstName', 'middleName', 'lastName'
    ];


    public function details() {
        return $this->hasMany('App\Models\EmployeeDetail');
    }

    public function employmentDetails() {
        return $this->hasMany('App\Models\EmploymentDetail');
    }

    public function deductibles() {
        return $this->hasMany('App\Models\EmployeeDeductible');
    }


    public function fullName() {
        $middleInitial = $this->middleName != '' ? substr($this->middleName, 0, 1).'.' : '';

        return $this->firstName.' '.$middleInitial.' '.$this->lastName;
    }


    public function pictures() {
        $rawPics = $this->hasMany('App\Models\EmployeePicture')->orderBy('updated_at', 'desc')->orderBy('isCurrent', 'desc');
        return $rawPics;
    }

 }
