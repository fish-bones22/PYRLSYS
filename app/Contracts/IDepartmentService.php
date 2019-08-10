<?php


namespace App\Contracts;

use App\Entities\DepartmentEntity;

interface IDepartmentService {

    public function getAllDepartments();
    public function getDepartmentById($id);
    public function addDepartment(DepartmentEntity $department);
    public function updateDepartment(DepartmentEntity $department);
    public function removeDepartment($id);

}
