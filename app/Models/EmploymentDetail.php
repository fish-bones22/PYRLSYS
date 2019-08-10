<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmploymentDetail extends Model
{
    protected $fillable = [
        'employee_id', 'category_id'
    ];

    public function category() {
        return $this->belongsTo('App\Models\Category');
    }
}
