<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeHistory extends Model
{
    protected $table = 'employment_histories';
    protected $fillable = [
        'timecard', 'position', 'employee_id', 'department', 'dateStarted', 'dateTransfered', 'current',
        'employmenttype', 'status', 'paymenttype', 'paymentmode', 'rate', 'allowance', 'timein', 'timeout'
    ];

    public function departmentDetails() {
        return $this->belongsTo('App\Models\Category', 'department', 'id');
    }

    public function employmentType() {
        return $this->belongsTo('App\Models\Category', 'employmenttype', 'id');
    }

    public function statusDetails() {
        return $this->belongsTo('App\Models\Category', 'status', 'id');
    }

    public function paymentType() {
        return $this->belongsTo('App\Models\Category', 'paymenttype', 'id');
    }

    public function paymentMode() {
        return $this->belongsTo('App\Models\Category', 'paymentmode', 'id');
    }
}
