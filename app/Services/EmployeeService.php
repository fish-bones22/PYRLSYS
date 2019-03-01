<?php

namespace App\Services;

use App\Contracts\IEmployeeService;
use App\Models\Employee;
use App\Models\EmployeeDeductible;
use App\Models\EmployeeDetail;
use App\Models\EmployeeHistory;
use App\Models\EmployeePicture;
use App\Models\EmploymentDetail;
use App\Entities\EmployeeEntity;
use AuthUtility;

class EmployeeService extends EntityService implements IEmployeeService {

    public function getAllEmployees($order = null) {

        if ($order)
            $employees = Employee::orderBy($order, 'asc')->get();
        else
            $employees = Employee::all();

        $employeeEntities = array();


        foreach ($employees as $emp) {

            $current = $emp->current;
            if ($current == null) {
                continue;
            }
            if (!AuthUtility::hasDepartmentAccess($current->first()['department']))
                continue;

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

        if ($emp == null)
            return null;

        $details = $emp->details;

        if (key_exists('applicant', $details))
            return null;

        return $this->mapToEntity($emp, new EmployeeEntity());
    }



    public function getEmployeeByName($name) {

        $employees = $this->getAllEmployees();
        $emp = null;

        if ($employees == null)
            return null;

        foreach ($employees as $employee) {
            if (\strpos(strtolower($employee->fullName), strtolower($name)) !== false) {
                $emp = $employee;
            }
            else if (\strpos(strtolower($employee->firstName.' '.$employee->lastName), strtolower($name)) !== false) {
                $emp = $employee;
            }
        }

        return $emp;
    }


    public function getEmployeeByIdWithStateOnDate($id, $date) {

        $employee = $this->getEmployeeById($id);

        if ($employee == null) return null;

        // Get current state
        $current = $employee->current;

        // Transform given date to time format
        $date_ = strtotime(date_format($date,'Y-m-d'));

        $earliest = null;
        $earliestHistory = null;

        // Iterate through employee's past states and present
        foreach ($employee->history as $history) {
            // Skip if this state has no datestarted value
            if (!isset($history['datestarted'])) {
                continue;
            }

            $start = isset($history['datestarted']) && $history['datestarted'] != null ? strtotime($history['datestarted']) : null;
            $end = isset($history['datetransfered']) && $history['datetransfered'] != null ? strtotime($history['datetransfered']) : null;

            // Get earliest date from the collection
            if ($earliest == null || $start <= $earliest) {
                $earliest = $start;
                $earliestHistory = $history;
            }

            // If no datetransfered (most likely, this is the current state)
            if ($end == null) {
                // If start date is earlier than given date,
                // Set this state as current
                if ($start <= $date_) {
                    $current = $history;
                    break;
                }
            }
            else {
                // date given is within the state's start and end date
                // set this state as current
                if ($start <= $date_ && $end > $date_) {
                    $current = $history;
                    break;
                }
            }
        }

        if ($date_ <= $earliest && $current == null) {
            $current = $earliestHistory;
        }

        $employee->current = $current;
        return $employee;
    }


    public function getEmployeesByDepartment($dept) {

        $employees = $this->getAllEmployees('lastName');

        if ($employees == null) return 'Hello';

        foreach ($employees as $key => $emp) {
            if ($dept == 0)
                break;
            if ($emp->current == null || !key_exists('department', $emp->current) || $emp->current['department']['value'] != $dept) {
                unset($employees[$key]);
            }
        }

        return $employees;
    }


    public function getApplicantById($id) {

        $app = Employee::find($id);

        if ($app == null)
            return null;

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
        //$entity->employmentDetails = $this->getEmploymentDetails($model->employmentDetails);
        $entity->deductibles = $this->getDeductibles($model->deductibles);

        // History
        //$entity->current = $this->getHistoryDetails($model->history->where('current', true)->first());
        $curr =  $this->getHistoryDetails($model->history->where('current', true)->first());
        $entity->current = $curr;
        foreach ($model->history as $history) {
            $entity->history[] = $this->getHistoryDetails($history);
        }

        return $entity;

    }


    public function getEmployeeHistoryOnDate($id, $date) {
        $histories = EmployeeHistory::where('employee_id', $id)->get();
        $current = null;

        if ($histories == null)
            return null;

        $dateStr = date_format($date, 'Y-m-d');
        $date_ = strtotime($dateStr);

        $earliest = null;
        $earliestHistory = null;

        foreach ($histories as $history) {

            if ($history->dateStarted == null) {
                continue;
            }

            // $start = strtotime($history->dateStarted);
            // $end = $history->dateTransfered != null ? strtotime($history->dateTransfered) : null;
            // $date_ = strtotime($date);
            $startStr = $history->dateStarted;
            $start = strtotime($startStr);
            $end = $history->dateTransfered != null ? strtotime($history->dateTransfered) : null;

            // Get earliest date from the collection
            if ($earliest == null || $start <= $earliest) {
                $earliest = $start;
                $earliestHistory = $history;
            }

            if ($end == null) {
                if ($start <= $date_) {
                    $current = $history;
                    break;
                }
            }
            else {
                if ($start <= $date_ && $end > $date_) {
                    $current = $history;
                    break;
                }
            }
        }

        if ($date_ <= $earliest && $current == null) {
            $current = $earliestHistory;
        }

        return $this->getHistoryDetails($current);
    }


    public function getCurrentEmployeeHistory($id) {
        $current = EmployeeHistory::where('employee_id', $id)->where('current', true)->first();

        if ($current == null)
            return null;

        return $this->getHistoryDetails($current);
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

        //$res = $this->saveEmploymentDetails($id, $entity->employmentDetails);

        if (!$res['result'])
            return $res;

        if ($employee->employeeId != null && $employee->employeeId != '') {
            $res = $this->addEmploymentHistory($id, $entity->current);

            if (!$res['result'])
                return $res;
        }

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

        if ($entity->employeeId != null && $entity->employeeId != '') {
            $res = $this->updateEmploymentHistory($id, $entity->current);

            if (!$res['result'])
                return $res;
        }

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


    public function transferEmployee($id, $employmentDetails) {
        return $this->addEmploymentHistory($id, $employmentDetails);
    }


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
            $detail[$cat->key] = [
                'value' => $detailModel->category_id,
                'displayName' => $detailModel->category->value
            ];
        }

        return $detail;

    }


    private function updateEmploymentHistory($id, $history) {

        $current = EmployeeHistory::where('employee_id', $id)->where('current', true)->first();
        if ($current == null)
            return $this->addEmploymentHistory($id, $history);
        $current->timecard = $history['timecard'];
        $current->position = $history['position'];
        $current->department = $history['department']['value'];
        $current->dateStarted = $history['datestarted'];
        $current->dateTransfered = $history['datetransfered'];
        $current->employmenttype = $history['employmenttype']['value'];
        $current->status = $history['contractstatus']['value'];
        $current->paymenttype = $history['paymenttype']['value'];
        $current->paymentmode = $history['paymentmode']['value'];
        $current->rate = $history['rate'];
        $current->rateBasis = isset($history['ratebasis']) ? $history['ratebasis'] : null;
        $current->allowance = isset($history['allowance']) ? $history['allowance'] : null;
        $current->timein = $history['timein'];
        $current->timeout = $history['timeout'];
        $current->break = $history['break'];

        try {
            $current->save();
        }
        catch (\Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage()
            ];
        }

        return [
            'result' => true
        ];

    }


    private function addEmploymentHistory($id, $history) {

        $new = new EmployeeHistory();
        $new->employee_id = $id;
        $new->timecard = $history['timecard'];
        $new->position = $history['position'];
        $new->department = $history['department']['value'];
        $new->dateStarted = $history['datestarted'];
        $new->dateTransfered = $history['datetransfered'];
        $new->employmenttype = $history['employmenttype']['value'];
        $new->status = $history['contractstatus']['value'];
        $new->paymenttype = $history['paymenttype']['value'];
        $new->paymentmode = $history['paymentmode']['value'];
        $new->rate = $history['rate'];
        $new->rateBasis = isset($history['ratebasis']) ? $history['ratebasis'] : null;
        $new->allowance = isset($history['allowance']) ? $history['allowance'] : null;
        $new->timein = $history['timein'];
        $new->timeout = $history['timeout'];
        $new->break = $history['break'];

        $new->current = true;

        $current = EmployeeHistory::where('employee_id', $id)->where('current', true)->first();
        if ($current != null) {
            $current->current = false;
            $current->dateTransfered = $history['datestarted'];
        }

        try {
            $new->save();
            if ($current != null)
                $current->save();
        }
        catch (\Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage()
            ];
        }

        return [
            'result' => true
        ];

    }


    private function getHistoryDetails($model) {

        if (is_null($model))
            return null;
        $history = array();
        $history['timecard'] = $model->timecard;
        $history['position'] = $model->position;
        $history['department'] = array();
        $history['department']['value'] = $model->department;
        $history['department']['displayName'] = $model->departmentDetails->value;
        $history['datestarted'] = $model->dateStarted;
        $history['datetransfered'] = $model->dateTransfered;
        $history['current'] = $model->current;
        $history['employmenttype'] = array();
        $history['employmenttype']['value'] = $model->employmenttype;
        $history['employmenttype']['displayName'] = $model->employmentType->value;
        $history['paymenttype'] = array();
        $history['paymenttype']['value'] = $model->paymenttype;
        $history['paymenttype']['displayName'] = $model->paymentType->value;
        $history['contractstatus'] = array();
        $history['contractstatus']['value'] = $model->status;
        $history['contractstatus']['displayName'] = $model->statusDetails->value;
        $history['paymentmode'] = array();
        $history['paymentmode']['value'] = $model->paymentmode;
        $history['paymentmode']['displayName'] = $model->paymentMode->value;
        $history['ratebasis'] = $model->rateBasis;
        $history['rate'] = $model->rate;
        $history['allowance'] = $model->allowance;
        $history['timein'] = $model->timein;
        $history['timeout'] = $model->timeout;
        $history['break'] = $model->break;

        return $history;
    }


    private function saveDeductibles($id, $deductibles) {

        $current = EmployeeDeductible::where('employee_id', $id);

        if ($current != null)
            $current->delete();

        foreach ($deductibles as $key => $deductible) {

            if (!isset($deductible['value'])) {
                continue;
            }

            $ded = new EmployeeDeductible();
            $ded->key = $key;
            $ded->value = $deductible['value'];
            $ded->isset = $deductible['isset'];
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
            $deductibles[$deductibleModel->key] = array();
            $deductibles[$deductibleModel->key]['value'] = $deductibleModel->value;
            $deductibles[$deductibleModel->key]['isset'] = $deductibleModel->isset;
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

    public function deleteAllEmployee() {
        $employee = Employee::whereNotNull('employeeId', null);
        foreach ($employee as $emp) {
            $emp->delete();
        }
    }



    public function deleteAllApplicant() {
        $employee = Employee::whereNull('employeeId', null);
        foreach ($employee as $emp) {
            $emp->delete();
        }
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


    public function idExists($id) {
        $result = Employee::where('employeeId', $id)->first();

        if ($result == null)
            return false;

        return true;
    }

}
