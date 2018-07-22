<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'key', 'value', 'detail'
    ];

    public function details() {
        return $this->belongsTo('App\Models\CategoryDetail', 'key', 'key');
    }
}
