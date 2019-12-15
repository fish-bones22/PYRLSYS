<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MiscPayable extends Model
{
    //
    protected $fillable = [
        'recordDate', 'amount', 'key', 'displayName', 'employee_id', 'employeeName', 'department_id', 'details'
    ];

    public function employee() {
        return $this->belongsTo('App\Models\Employee');
    }

    public function department() {
        return $this->belongsTo('App\Models\Category');
    }
}
