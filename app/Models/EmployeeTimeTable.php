<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeTimeTable extends Model
{
    protected $fillable = [
        'id', 'employee_id', 'timeIn', 'timeOut', 'break', 'startDate', 'endDate'
    ];

    public function employee() {
        return $this->belongsTo('App\Models\Employee', 'employee_id', 'id');
    }
}
