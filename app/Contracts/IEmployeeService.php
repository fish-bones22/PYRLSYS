<?php

namespace App\Contracts;

use App\Entities\EmployeeEntity;

interface IEmployeeService {

    public function getAllApplicants();
    public function getApplicantById($id);
    public function checkApplicant($firstName, $middleName, $lastName, $position);

    public function getAllEmployees($order = null);
    public function getEmployeeById($id);
    public function getEmployeesByDepartment($dept);
    public function searchEmployeeByName($name);
    public function getEmployeeByName($name);
    public function getEmployeeByEmployeeId($employeeId);
    public function getIdByEmployeeId($id);

    public function updateDetail($id, $key, $value);
    public function removeDetail($id, $key);
    public function addEmployee(EmployeeEntity $employee);
    public function updateEmployee(EmployeeEntity $employee);
    public function removeEmployee($id);
    public function removeEmployeeImage($id, $location, $filename);
    public function addEmployeeImage($id, $location, $filename);
    public function setEmployeeImage($id, $location, $filename);
    public function unsetCurrentEmployeeImage ($id);
    public function transferEmployee($id, $employmentDetails);
    public function addEmployeeTimeTable($id, $timeTable);

    public function getEmployeeHistoryOnDate($employeeId, $date);
    public function getEmployeeTimeTable($employeeId, $date);
    public function getCurrentEmployeeHistory($employeeId);
    public function getEmployeeByIdWithStateOnDate($employeeId, $date);
    public function getEmployeePayTable($employeeId, $date);
    public function idExists($id);


    public function deleteAllEmployee();
    public function deleteInactive();
}
