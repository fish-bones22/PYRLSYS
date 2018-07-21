<?php

namespace App\Services;

use App\Contracts\IUserRoleService;
use App\Entities\UserRoleEntity;
use App\Models\UserRole;

class UserRoleService extends EntityService implements IUserRoleService {

    public function getAllRoles() {

        $roles = UserRole::all();
        $rolesEntity = array();

        foreach ($roles as $role) {
            $rolesEntity[] = $this->mapToEntity($role, new UserRoleEntity());
        }

        return $rolesEntity;

    }

    protected function mapToEntity($model, $entity)  {
        $entity = parent::mapToEntity($model, $entity);
        $entity->description = $model->description;
        $entity->roleName = $model->roleName;
        $entity->roleKey = $model->roleKey;

        return $entity;
    }
}
