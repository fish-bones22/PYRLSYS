<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class DepartmentAccess extends Authenticatable
{
    use Notifiable;

    protected $table = 'user_department_accesses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'category_id',
    ];

    public function category() {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
