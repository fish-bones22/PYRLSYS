<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePicture extends Model
{
    //
    protected $fillable = [
        'location', 'filename', 'isCurrent'
    ];
}
