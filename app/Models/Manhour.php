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

    public function department() {
        return $this->belongsTo('App\Models\Category');
    }

    public function outlier() {
        return $this->belongsTo('App\Models\Category');
    }
}
