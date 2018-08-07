<?php

namespace App\Contracts;

use App\Entities\ManhourEntity;

interface IManhourService {

    public function recordManhour(ManhourEntity $entity);
    public function getRecord($id, $date);

}
