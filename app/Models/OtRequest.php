<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtRequest extends Model
{
    protected $fillable = [
        'otDate', 'timeStart', 'timeEnd', 'department', 'employee_id', 'employeeName',
        'allowedHours', 'reason', 'approval'
    ];

    public function employee() {
        return $this->belongsTo('App\Models\Employee');
    }

    public function departmentDetails() {
        return $this->belongsTo('App\Models\Category', 'department', 'id');
    }
}
