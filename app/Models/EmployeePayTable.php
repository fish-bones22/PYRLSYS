<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePayTable extends Model
{
    protected $table = 'employee_pay_table';

    protected $fillable = [
        'id', 'employee_id', 'rate', 'allowance', 'rateBasis', 'paymentmode', 'startDate', 'endDate'
    ];

    public function employee() {
        return $this->belongsTo('App\Models\Employee', 'employee_id', 'id');
    }

    public function paymentMode() {
        return $this->belongsTo('App\Models\Category', 'paymentmode', 'id');
    }
}
