<?php

namespace App\Services;

use App\Contracts\IUserRoleService;
use App\Entities\UserRoleEntity;
use App\Models\UserRole;

class UserRoleService implements IUserRoleService {

    public function getAllRoles() {

        $roles = UserRole::all();
        $rolesEntity = array();

        foreach ($roles as $role) {
            $rolesEntity[] = $this->mapToEntity($role);
        }

        return $rolesEntity;

    }

    private function mapToEntity($model)  {
        $entity = new UserRoleEntity();
        $entity->id = $model->id;
        $entity->description = $model->description;
        $entity->roleName = $model->roleName;
        $entity->roleKey = $model->roleKey;

        return $entity;
    }
}
