<?php

namespace App\Entities;

use App\Models\Category;

class CategoryEntity extends Entity {
    public $key;
    public $value;
    public $detail;
    public $subvalue1;
    public $subvalue2;
    public $subvalue3;

    public $displayName;
    public $description;

    public $timeTable = array();

}
