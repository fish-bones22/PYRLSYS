<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manhour extends Model
{
    //
    protected $fillable = [
        'recordDate', 'timeIn', 'timeOut', 'employee_id', 'employeeName', 'timecard', 'department', 'outlier', 'remarks'
    ];

    public function employee() {
        return $this->belongsTo('App\Models\Employee');
    }

    public function departmentDetails() {
        return $this->belongsTo('App\Models\Category', 'department', 'id');
    }

    public function outlierDetails() {
        return $this->belongsTo('App\Models\Category', 'outlier', 'id');
    }
}
