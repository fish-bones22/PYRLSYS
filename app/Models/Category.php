<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'key', 'value', 'detail', 'subvalue1', 'subvalue2', 'subvalue2'
    ];

    public function details() {
        return $this->belongsTo('App\Models\CategoryDetail', 'key', 'key');
    }

    public function timeTable() {
        return $this->hasMany('App\Models\DepartmentTimeTable', 'department_id', 'id');
    }
}
