<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeductibleRecord extends Model
{
    protected $fillable = [
        'employee_id', 'employeeName', 'identifier', 'identifierDetails', 'deductible_id',
        'recordDate', 'details', 'amount', 'subamount', 'remarks'
    ];

    public function employee() {
        return $this->belongsTo('App\Models\Employee', 'employee_id', 'id');
    }

    public function deductible() {
        return $this->belongsTo('App\Models\Deductible');
    }
}
