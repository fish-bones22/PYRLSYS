<?php

namespace App\Contracts;

use App\Entities\EmployeeEntity;

interface IEmployeeService {

    public function getAllEmployees();
    public function getEmployeeById($id);
    public function addEmployee(EmployeeEntity $employee);
    public function updateEmployee(EmployeeEntity $employee);
    public function removeEmployee($id);
    public function removeEmployeeImage($id, $location, $filename);
    public function addEmployeeImage($id, $location, $filename);
    public function setEmployeeImage($id, $location, $filename);
    public function unsetCurrentEmployeeImage ($id);
}
