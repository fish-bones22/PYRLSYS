<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeductiblesRecord extends Model
{
    protected $fillable = [
        'employee_id', 'employeeName', 'identifier', 'identifierDetails', 'deductible_id',
        'recordDate', 'details', 'amount', 'subamount', 'remarks'
    ];

    public function employee() {
        return $this->belongsTo('App\Models\Employee');
    }

    public function deductible() {
        return $this->belongsTo('App\Models\Deductible');
    }
}
