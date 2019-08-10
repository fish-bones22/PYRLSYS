<?php

namespace App\Services;

use App\Contracts\IDepartmentService;
use App\Entities\DepartmentEntity;
use App\Models\Department;

class DepartmentService extends EntityService implements IDepartmentService {

    public function getAllDepartments() {
        $departments = Department::all();
        $deptEntities = array();

        foreach($departments as $department) {
            $deptEntities[] = $this->mapToEntity($department, new DepartmentEntity());
        }

        return $deptEntities;
    }


    public function getDepartmentById($id) {

        $department = Department::find($id);

        return $this->mapToEntity($department, new DepartmentEntity());

    }


    protected function mapToEntity($model, $entity) {
        $entity = parent::mapToEntity($model, $entity);
        $entity->name = $model->name;
        $entity->description = $model->description;

        return $entity;
    }


    public function addDepartment(DepartmentEntity $department) {

        $departmentModel = new Department();

        $departmentModel->name = $department->name;
        $departmentModel->description = $department->description;

        $departmentModel->save();

    }


    public function updateDepartment(DepartmentEntity $department) {

        $departmentModel = Department::find($department->id);

        $departmentModel->name = $department->name;
        $departmentModel->description = $department->description;

        $departmentModel->save();
    }


    public function removeDepartment($id) {

        $departmentModel = Department::find($id);

        $departmentModel->delete();

    }


}
