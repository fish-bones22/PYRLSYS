<?php

namespace App\Services;

use App\Contracts\IEmployeeService;
use App\Models\Employee;
use App\Models\EmployeeDetail;
use App\Models\EmployeePicture;
use App\Entities\EmployeeEntity;

class EmployeeService extends EntityService implements IEmployeeService {

    public function getAllEmployees() {
        $employees = Employee::all();
        $employeeEntities = array();
        foreach ($employees as $emp) {
            $employeeEntities[] = $this->mapToEntity($emp, new EmployeeEntity());
        }

        return $employeeEntities;
    }


    public function getEmployeeById($id) {
        $emp = Employee::find($id);

        return $this->mapToEntity($emp, new EmployeeEntity());
    }


    protected function mapToEntity($model, $entity) {

        $entity = parent::mapToEntity($model, $entity);

        $entity->fullName = $model->fullName();
        $entity->firstName = $model->firstName;
        $entity->middleName = $model->middleName;
        $entity->lastName = $model->lastName;
        $entity->employeeId = $model->employeeId;

        // Pictures
        $entity->pictures = array();
        foreach ($model->pictures as $pic) {
            $entry = [
                'location' => $pic->location,
                'filename' => $pic->filename,
                'isCurrent' => $pic->isCurrent
            ];

            if ($pic->isCurrent == 1)
                $entity->currentPicture = $entry;

            $entity->pictures[] = $entry;
        }

        // Details
        $entity->otherContacts = array();
        $entity->details = array();
        foreach ($model->details as $detail) {

            $entry = [
                'id' => $detail->id,
                'key' => $detail->key,
                'displayName' => $detail->displayName,
                'value' => $detail->value,
                'detail' => $detail->detail
            ];

            if ($detail->key == 'contact') {
                // Primary contact
                if ($detail->detail == 'primary')
                    $entity->contactNumber = $detail->value;
                // other contact details
                else
                    $entity->otherContacts[] = $entry;
            }
            // primary email
            else if ($detail->key == 'email') {
                $entity->email = $detail->value;
            }
            // other details
            else {
                $entity->details[] = $entry;
            }
        }

        return $entity;

    }



    public function addEmployee(EmployeeEntity $entity) {

        $employee = new Employee();
        $employee->employeeId = $entity->employeeId;
        $employee->firstName = $entity->firstName;
        $employee->middleName = $entity->middleName;
        $employee->lastName = $entity->lastName;
        $employee->save();
        // Get Id
        $id = Employee::orderBy('created_at', 'desc')->first()->id;

        // primary contact number
        if ($entity->contactNumber != null) {
            $contact = new EmployeeDetail();
            $contact->key = 'contact';
            $contact->detail = 'primary';
            $contact->displayName = 'Contact Number';
            $contact->value = $entity->contactNumber;
            $contact->employee_id = $id;
            $contact->save();
        }

        // primary email
        if ($entity->email != null) {
            $email = new EmployeeDetail();
            $email->key = 'email';
            $email->detail = 'primary';
            $email->displayName = 'Email Address';
            $email->value = $entity->email;
            $email->employee_id = $id;
            $email->save();
        }

        // other details
        $this->saveDetails($id, $entity->details);

        return $id;
    }


    public function updateEmployee(EmployeeEntity $entity) {
        $id = $entity->id;
        $employee = Employee::find($id);
        $employee->employeeId = $entity->employeeId != null ? $entity->employeeId : $employee->employeeId;
        $employee->firstName = $entity->firstName != null ? $entity->firstName : $employee->firstName;
        $employee->middleName = $entity->middleName;
        $employee->lastName = $entity->lastName != null ? $entity->lastName : $employee->lastName;
        $employee->save();

        // primary contact number
        if ($entity->contactNumber != null) {
            $contact = EmployeeDetail::where('employee_id', $id)->where('key', 'contact')->where('detail', 'primary')->first();
            $contact->value = $entity->contactNumber;
            $contact->save();
        }

        // primary email
        if ($entity->email != null) {
            $email = EmployeeDetail::where('employee_id', $id)->where('key', 'email')->where('detail', 'primary')->first();
            $email->value = $entity->email;
            $email->save();
        }

        // other details
        $this->saveDetails($id, $entity->details);
    }

    private function saveDetails($id, $detailsArray) {

        if (sizeof($detailsArray) != 0) {
            foreach($detailsArray as $detail) {

                $det;
                if ($detail['id'] == 0)
                    $det = new EmployeeDetail();
                else
                    $det = EmployeeDetail::where('id', $detail['id'])->first();

                $det->key = $detail['key'];
                $det->detail = $detail['detail'];
                $det->displayName = $detail['displayName'];
                $det->value = $detail['value'];
                $det->employee_id = $id;
                $det->save();
            }
        }

    }


    public function removeEmployee($id) {
        $emp = Employee::find($id);
        $emp->delete();
    }

    public function removeEmployeeImage($id, $location, $filename) {

        $currentPic = EmployeePicture::where('employee_id', $id)->where('location', $location)->where('filename', $filename)->first();
        if ($currentPic) {
            $currentPic->delete();
        }

    }

    public function addEmployeeImage($id, $location, $filename) {

        $this->unsetCurrentEmployeeImage($id);

        // Save data to DB
        $employeePic = new EmployeePicture;
        $employeePic->employee_id = $id;
        $employeePic->location = addslashes($location);
        $employeePic->filename = addslashes($filename);
        $employeePic->isCurrent = true;
        $employeePic->save();
    }

    public function setEmployeeImage($id, $location, $filename) {

        $this->unsetCurrentEmployeeImage($id);

        $newPic = EmployeePicture::where('employee_id', $id)->where('filename', $filename)->where('location', $location)->first();
        if ($newPic) {
            $newPic->isCurrent = true;
            $newPic->save();
        }

    }

    public function unsetCurrentEmployeeImage ($id) {

        // Set current picture to not current
        $currentPic = EmployeePicture::where('employee_id', $id)->where('isCurrent', true)->first();
        if ($currentPic) {
            $currentPic->isCurrent = false;
            $currentPic->save();
        }

    }

}
