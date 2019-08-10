<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryDetail extends Model
{
    protected $fillable = [
        'key', 'displayName', 'description'
    ];

    public function sub() {
        return $this->hasMany('App\Models\Category');
    }
}
