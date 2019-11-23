<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'password', 'admin', 'fullName'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    protected $rememberTokenName = false;

    public function accesses()
    {
        return $this->hasMany('App\Models\UserAccess');
    }

    public function departmentAccesses()
    {
        return $this->hasMany('App\Models\DepartmentAccess');
    }

    public function GetSessionAccessesAttribute()
    {
        return $this->getAccesses();
    }

    public function GetSessionDepartmentsAttribute()
    {
        return $this->getDepartmentAccesses();
    }

    private function getAccesses()
    {
        $accesses =  $this->hasMany('App\Models\UserAccess')->get();
        $acc = array();
        foreach ($accesses as $access) {
            $acc[] = $access->details->roleKey;
        }
        return $acc;
    }

    private function getDepartmentAccesses()
    {
        $accesses =  $this->hasMany('App\Models\DepartmentAccess')->get();
        $acc = array();
        foreach ($accesses as $access) {
            $acc[] = $access->category_id;
        }
        return $acc;
    }
}
