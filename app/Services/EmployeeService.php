<?php

namespace App\Services;

use App\Contracts\IEmployeeService;
use App\Models\Employee;
use App\Models\EmployeeDetail;
use App\Models\EmploymentDetail;
use App\Models\EmployeeDeductible;
use App\Models\EmployeePicture;
use App\Entities\EmployeeEntity;

class EmployeeService extends EntityService implements IEmployeeService {

    public function getAllEmployees() {

        $employees = Employee::all();
        $employeeEntities = array();

        foreach ($employees as $emp) {

            $detail = $emp->details;

            if ($detail->where('key', 'applicant')->first() != null)
                continue;

            $employeeEntities[] = $this->mapToEntity($emp, new EmployeeEntity());
        }

        return $employeeEntities;
    }


    public function getAllApplicants() {

        $applicants = Employee::whereNull('employeeId')->get();
        $applicantEntities = array();

        foreach ($applicants as $app) {
            $applicantEntities[] = $this->mapToEntity($app, new EmployeeEntity());
        }

        return $applicantEntities;
    }


    public function getEmployeeById($id) {
        $emp = Employee::find($id);
        $details = $emp->details;

        if (key_exists('applicant', $details))
            return null;

        return $this->mapToEntity($emp, new EmployeeEntity());
    }


    public function getApplicantById($id) {
        $app = Employee::find($id);
        $details = $app->details;

        if (!key_exists('applicant', $details))
            return null;

        return $this->mapToEntity($app, new EmployeeEntity());
    }


    protected function mapToEntity($model, $entity) {

        $entity = parent::mapToEntity($model, $entity);

        $entity->fullName = $model->fullName();
        $entity->firstName = $model->firstName;
        $entity->middleName = $model->middleName;
        $entity->lastName = $model->lastName;
        $entity->sex = $model->sex;
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
        $entity->details = $this->getDetails($model->details);
        $entity->employmentDetails = $this->getEmploymentDetails($model->employmentDetails);
        $entity->deductibles = $this->getDeductibles($model->deductibles);
        return $entity;

    }


    public function checkApplicant($firstName, $middleName, $lastName, $position) {

        $entity = Employee::where('firstName', $firstName)->where('middleName', $middleName)->where('lastName', $lastName)->whereNull('employeeId')->first();

        if ($entity == null)
            return false;

        $details = $entity->details;

        if (key_exists('position', $details) && $details['position'] == $position)
            return false;

        return true;
    }


    public function addEmployee(EmployeeEntity $entity) {

        $employee = new Employee();
        $employee->employeeId = $entity->employeeId;
        $employee->firstName = $entity->firstName;
        $employee->middleName = $entity->middleName;
        $employee->lastName = $entity->lastName;
        $employee->sex = $entity->sex;

        try {
            $employee->save();
        } catch (\Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage()
            ];
        }
        // Get Id
        $id = Employee::orderBy('created_at', 'desc')->first()->id;

        // other details
        $oldDetails = EmployeeDetail::where('employee_id', $id);
        if ($oldDetails != null)
            $oldDetails->delete();

        $res = $this->saveDetails($id, '', $entity->details);

        if (!$res['result'])
            return $res;

        $res = $this->saveEmploymentDetails($id, $entity->employmentDetails);

        if (!$res['result'])
            return $res;

        $res = $this->saveDeductibles($id, $entity->deductibles);

        return [
            'result' => $id
        ];
    }


    public function updateEmployee(EmployeeEntity $entity) {

        $id = $entity->id;
        $employee = Employee::find($id);
        $employee->employeeId = $entity->employeeId != null ? $entity->employeeId : $employee->employeeId;
        $employee->firstName = $entity->firstName != null ? $entity->firstName : $employee->firstName;
        $employee->middleName = $entity->middleName;
        $employee->lastName = $entity->lastName != null ? $entity->lastName : $employee->lastName;
        $employee->sex = $entity->sex != null ? $entity->sex : $employee->sex;

        try {
            $re = $employee->save();
            $re;
        } catch (\Illuminate\Database\QueryException $e) {
            return [
                'result' => false,
                'message' => $e->getMessage()
            ];
        }

        // other details
        $oldDetails = EmployeeDetail::where('employee_id', $id);
        if ($oldDetails != null)
            $oldDetails->delete();

        $res = $this->saveDetails($id, '', $entity->details);

        if (!$res['result'])
            return $res;

        $res = $this->saveEmploymentDetails($id, $entity->employmentDetails);

        if (!$res['result'])
            return $res;

        $res = $this->saveDeductibles($id, $entity->deductibles);

        return $res;
    }


    public function updateDetail($id, $key, $value) {

        $detail = EmployeeDetail::where('key', $key)->where('employee_id', $id)->first();

        if ($detail == null)
            return [
                'result' => false,
                'message' => 'No details found'
            ];

        $detail->value = $value;
        $detail->save();

        return [
            'result' => true
        ];

    }


    public function removeDetail($id, $key) {

        $detail = EmployeeDetail::where('key', $key)->where('employee_id', $id)->first();

        if ($detail == null)
            return [
                'result' => false,
                'message' => 'No details found'
            ];

        $detail->delete();

        return [
            'result' => true
        ];

    }


    private function saveDetails($id, $key, $detailsArray) {

        if (!is_array($detailsArray)) {
            return [
                'result' => false,
                'message' => 'No details found'
            ];
        }

        foreach ($detailsArray as $subkey => $detail) {

            if (is_array($detail)) {

                $this->saveDetails($id, $key.($key != '' ? '.' : '').$subkey, $detail);

            } else {

                $det = new EmployeeDetail();
                $det->key = $key;
                $det->grouping = key_exists('grouping', $detailsArray) ? $detailsArray['grouping'] : null;
                $det->detail = key_exists('detail', $detailsArray) ? $detailsArray['detail'] : null;
                $det->displayName = $detailsArray['displayName'];
                $det->value = $detailsArray['value'];
                $det->employee_id = $id;

                try {
                    $det->save();
                } catch (\Exception $e) {
                    return [
                        'result' => false,
                        'message' => $e->getMessage()
                    ];
                }

                return [
                    'result' => true
                ];

            }
        }

        return [
            'result' => true
        ];

    }

    // public function getDetails($detailsModel) {

    //     $detail = array();
    //     $prevKey = '';
    //     foreach ($detailsModel as $model) {

    //         // If Key is compound
    //         if (strpos($model->key, '.') !== false) {

    //             $keys = explode('.', $model->key);

    //             if ($prevKey != $keys[0]) {
    //                 $temp = array();
    //                 $inner = array();
    //             }

    //             $inner[$keys[sizeof($keys)-1]] = [
    //                 'key' => $keys[sizeof($keys)-1],
    //                 'value' => $model->value,
    //                 'detail' => $model->detail,
    //                 'displayName' => $model->displayName,
    //                 'grouping' => $model->grouping
    //             ];

    //             $temp;
    //             $lastKey;
    //             for ($i = sizeof($keys)-1; $i >= 1; $i--) {
    //                 $temp = array();
    //                 $temp[$keys[$i]] = $inner;
    //                 $inner = $temp[$keys[$i]];
    //                 $lastKey = $keys[$i];
    //             }

    //             if (!key_exists($keys[0], $detail)) {
    //                 $detail[$keys[0]] = array();
    //             }

    //             if (sizeof($temp) > 0)
    //                 $detail[$keys[0]][$lastKey] = $temp[$lastKey];


    //             $prevKey = $keys[0];

    //         }
    //         else {
    //             $detail[$model->key] = [
    //                 'key' => $model->key,
    //                 'value' => $model->value,
    //                 'detail' => $model->detail,
    //                 'displayName' => $model->displayName,
    //                 'grouping' => $model->grouping
    //             ];
    //         }

    //     }

    //     return $detail;

    // }

    public function getDetails($detailsModel) {

        $detail = array();
        $prevKey = '';
        foreach ($detailsModel as $model) {

            // If Key is compound
            if (strpos($model->key, '.') !== false) {

                $keys = explode('.', $model->key);

                $inner = [
                    'key' => $keys[sizeof($keys)-1],
                    'value' => $model->value,
                    'detail' => $model->detail,
                    'displayName' => $model->displayName,
                    'grouping' => $model->grouping
                ];

                $tempArr = array();
                $detArr = $detail;
                for ($i = sizeof($keys) - 1; $i >= 0; $i--) {

                    // if (!key_exists($keys[$i], $tempArr)) {
                    //     $tempArr[$keys[$i]]
                    // }
                    $tempArr = array();
                    $tempArr[$keys[$i]] = $inner;
                    $inner = $tempArr;

                }

                if (!key_exists($keys[0], $detail))
                    $detail[$keys[0]] = array();
                $detail[$keys[0]] = array_replace_recursive($tempArr[$keys[0]], $detail[$keys[0]]);

                //$det = $this->fillDetailArray($detail, $tempArr);
                $detail = $detail;

            }
            else {
                $detail[$model->key] = [
                    'key' => $model->key,
                    'value' => $model->value,
                    'detail' => $model->detail,
                    'displayName' => $model->displayName,
                    'grouping' => $model->grouping
                ];
            }

        }

        return $detail;

    }


    private function fillDetailArray($detailArray, $tempArray) {

        foreach ($tempArray as $key => $value) {

            if (is_array($value)) {

                if (!key_exists($key, $detailArray)) {
                    $detailArray[$key] = array();
                }

                $detailArray[$key] = $this->fillDetailArray($detailArray[$key], $value);

            }
            else {

                if (!key_exists($key, $detailArray)) {
                    $detailArray[$key] = array();
                }
                $detailArray[$key] = $tempArray[$key];
            }
        }

        return $detailArray;

    }


    private function saveEmploymentDetails($id, $detailsArray) {

        foreach ($detailsArray as $detail) {

            $oldDetails = EmploymentDetail::where('employee_id', $id)->where('category_id', $detail);

            if ($oldDetails != null)
                $oldDetails->delete();

            $det = new EmploymentDetail();
            $det->category_id = $detail;
            $det->employee_id = $id;

            try {
                $det->save();
            }
            catch (\Exception $e) {
                return [
                    'result' => false,
                    'message' => $e->getMessage()
                ];
            }

        }

        return [
            'result' => true
        ];

    }

    private function getEmploymentDetails($detailsModel) {

        $detail = array();
        foreach ($detailsModel as $detailModel) {
            $cat = $detailModel->category;
            $detail[$cat->key] = $detailModel->category_id;
        }

        return $detail;

    }

    private function saveDeductibles($id, $deductibles) {

        $current = EmployeeDeductible::where('employee_id', $id);

        if ($current != null)
            $current->delete();

        foreach ($deductibles as $key => $deductible) {

            $ded = new EmployeeDeductible();
            $ded->key = $key;
            $ded->value = $deductible;
            $ded->employee_id = $id;

            try {
                $ded->save();
            } catch (\Exception $e) {
                return [
                    'result' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        return [
            'result' => true
        ];

    }


    public function getDeductibles($deductiblesModel) {

        $deductibles = array();

        foreach ($deductiblesModel as $deductibleModel) {
            $deductibles[$deductibleModel->key] = $deductibleModel->value;
        }

        return $deductibles;

    }

    public function removeEmployee($id) {

        $emp = Employee::find($id);

        try {
            $emp->delete();
        } catch (\Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage()
            ];
        }

        return [
            'result' => true
        ];
    }


    public function removeEmployeeImage($id, $location, $filename) {

        $currentPic = EmployeePicture::where('employee_id', $id)->where('location', $location)->where('filename', $filename)->first();

        if ($currentPic) {
            try {
                $currentPic->delete();
            } catch (\Exception $e) {
                return [
                    'result' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        return [
            'result' => true
        ];

    }


    public function addEmployeeImage($id, $location, $filename) {

        $this->unsetCurrentEmployeeImage($id);

        // Save data to DB
        $employeePic = new EmployeePicture;
        $employeePic->employee_id = $id;
        $employeePic->location = addslashes($location);
        $employeePic->filename = addslashes($filename);
        $employeePic->isCurrent = true;

        try {
            $employeePic->save();
        } catch (\Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage()
            ];
        }


        return [
            'result' => true
        ];
    }


    public function setEmployeeImage($id, $location, $filename) {

        $this->unsetCurrentEmployeeImage($id);

        $newPic = EmployeePicture::where('employee_id', $id)->where('filename', $filename)->where('location', $location)->first();
        if ($newPic) {
            $newPic->isCurrent = true;
            try {
                $newPic->save();
            } catch (\Exception $e) {
                return [
                    'result' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        return [
            'result' => true
        ];
    }


    public function unsetCurrentEmployeeImage ($id) {

        // Set current picture to not current
        $currentPic = EmployeePicture::where('employee_id', $id)->where('isCurrent', true)->first();
        if ($currentPic) {
            $currentPic->isCurrent = false;
            try {
                $currentPic->save();
            }  catch (\Exception $e) {
                return [
                    'result' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        return [
            'result' => true
        ];

    }

}
