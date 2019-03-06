<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentTimeTable extends Model
{
    protected $fillable = [
        'id', 'department_id', 'timeIn', 'timeOut', 'break',  'startDate', 'endDate'
    ];

    public function department() {
        return $this->belongsTo('App\Models\Category', 'department_id', 'id');
    }
}
