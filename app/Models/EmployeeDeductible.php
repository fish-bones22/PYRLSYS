<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDeductible extends Model
{
    //
    protected $fillable = [
        'key', 'employee_id', 'value', 'isset'
    ];

    public function details() {
        return $this->belongsTo('App\Models\Deductible', 'key', 'key');
    }
}
