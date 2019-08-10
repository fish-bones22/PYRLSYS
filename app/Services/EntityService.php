<?php

namespace App\Services;

use App\Entities\IEntity;

class EntityService {

    protected function mapToEntity($model, $entity) {
        $entity->id = $model->id;
        $entity->dateCreated = $model->created_at;
        $entity->dateUpdated = $model->updated_at;

        return $entity;
    }

}
