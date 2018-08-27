<?php

namespace App\Entities;

class UserEntity extends Entity {

    public $id = 0;
    public $fullName;
    public $username;
    public $password;
    public $admin;
    public $accesses;
    public $departmentAccesses;

}
