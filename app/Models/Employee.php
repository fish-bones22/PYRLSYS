<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function miscPayables() {
        return $this->miscPayments('App\Models\MiscPayables');
    }

    public function hasDeductible($key) {
        $res = DB::select('SELECT emp.id FROM employees AS emp INNER JOIN employee_deductibles as ded ON emp.id = ded.employee_id WHERE ded.key = \''.$key.'\'');
        return sizeof($res) > 0;
    }

    public function isInactive() {
        $res = DB::select('SELECT emp.id FROM employees AS emp INNER JOIN employment_histories AS his ON emp.id = his.employee_id INNER JOIN categories AS cat ON his.status = cat.id WHERE cat.Value = \'Inactive\' AND emp.id = '.$this->id);
        return sizeof($res) > 0;
    }

    public static function deleteInactive() {
        return DB::delete('DELETE emp, his FROM employees AS emp INNER JOIN employment_histories AS his ON emp.id = his.employee_id INNER JOIN categories AS cat ON his.status = cat.id WHERE cat.Value = \'Inactive\'');
    }
 }
