<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdjustmentsRecord extends Model
{
    protected $fillable = [
        'employee_id', 'employeeName', 'recordDate', 'key', 'details', 'amount', 'remarks', 'taxSchedule'
    ];

    public function employee() {
        return $this->belongsTo('App\Models\Employee', 'employee_id', 'id');
    }
}
