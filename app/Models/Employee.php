<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function timeTable() {
        return $this->hasMany('App\Models\EmployeeTimeTable')->orderBy('startDate');
    }

    public function payTable() {
        return $this->hasMany('App\Models\EmployeePayTable')->orderBy('startDate');
    }

    public function fullName() {
        $middleInitial = $this->middleName != '' ? substr($this->middleName, 0, 1).'.' : '';

        // return $this->firstName.' '.$middleInitial.' '.$this->lastName;
        return $this->lastName.', '.$this->firstName.' '.$middleInitial;
    }

    public function pictures() {
        $rawPics = $this->hasMany('App\Models\EmployeePicture')->orderBy('updated_at', 'desc')->orderBy('isCurrent', 'desc');
        return $rawPics;
    }

    public function history() {
        return $this->hasMany('App\Models\EmployeeHistory');
    }

    public function current() {
        return $this->hasMany('App\Models\EmployeeHistory')->where('current', true);
    }

 }
