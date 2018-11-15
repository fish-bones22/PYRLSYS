<?php

namespace App\Services;

use App\Contracts\IManhourService;
use App\Contracts\IOTRequestService;
use App\Contracts\IEmployeeService;
use App\Contracts\ICategoryService;
use App\Entities\EmployeeEntity;
use App\Entities\ManhourSummaryEntity;
use App\Entities\ManhourEntity;
use App\Models\Manhour;

class ManhourService extends EntityService implements IManhourService {

    private $otRequestService;
    private $employeeService;
    private $categoryService;

    public function __construct(IOtRequestService $otRequestService, IEmployeeService $employeeService, ICategoryService $categoryService) {
        $this->otRequestService = $otRequestService;
        $this->employeeService = $employeeService;
        $this->categoryService = $categoryService;
    }

    public function getAllRecords() {
        $records = Manhour::all();
        if ($records == null) return null;
        $recordsEntity = array();
        foreach ($records as $record) {
            $recordsEntity[] = $this->mapToEntity($record, new ManhourEntity());
        }

        return $recordsEntity;
    }


    public function getAllRecordsByDateRange($datefrom, $dateto) {

        $records = Manhour::whereBetween('recordDate', [$datefrom, $dateto])->get();

        if ($records == null) return null;

        $recordsEntity = array();
        foreach ($records as $record) {
            $recordsEntity[] = $this->mapToEntity($record, new ManhourEntity());
        }

        return $recordsEntity;
    }


    public function recordManhour(ManhourEntity $entity) {

        $record = Manhour::where('recordDate', $entity->date)->where('employee_id', $entity->employee_id)->first();

        // Delete if passed time in is blank
        if (($entity->timeIn == null || $entity->timeIn == '') && $entity->outlier == null) {
            if ($record != null) {
                $record->delete();
            }
            return [
                'result' => true
            ];
        }

        if ($record == null) {
            $record = new Manhour();
            $record->recordDate = $entity->date;
            $record->employee_id = $entity->employee_id;
        }

        $record->timeIn = $entity->timeIn;
        $record->timeOut = $entity->timeOut;

        $record->employeeName = $entity->employeeName;
        $record->timeCard = $entity->timeCard;
        $record->department = $entity->department;

        $record->authorized = $entity->authorized;
        $record->outlier = $entity->outlier;
        $record->remarks = addslashes($entity->remarks);

        try {
            $record->save();
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

    protected function mapToEntity($model, $entity) {

        $entity = parent::mapToEntity($model, $entity);

        $entity->date = $model->recordDate;
        $entity->timeIn = $model->timeIn;
        $entity->timeOut = $model->timeOut;

        $entity->employeeId = $model->employee_id;
        $entity->employeeName = $model->employeeName;
        $entity->timeCard = $model->timeCard;

        if ($model->department != null)
            $entity->department = [
                'value' => $model->department,
                'displayName' => $model->departmentDetails->value
            ];

        $entity->authorized = $model->authorized;
        if ($model->outlier != null)
            $entity->outlier = [
                'value' => $model->outlier,
                'displayName' => $model->outlierDetails->value
            ];
        $entity->remarks = stripslashes($model->remarks);

        return $entity;

    }


    public function getRecord($id, $date) {

        $record = Manhour::where('employee_id', $id)->where('recordDate', $date)->first();

        if ($record == null)
            return null;

        return $this->mapToEntity($record, new ManhourEntity());

    }

    public function getOutliersOnDateRange($employeeId, $datefrom, $dateto) {
        $records = Manhour::where('employee_id', $employeeId)->whereBetween('recordDate', [$datefrom, $dateto])->get();

        if ($records == null) return null;

        $recordsEntity = array();
        foreach ($records as $record) {
            $recordsEntity[] = $this->mapToEntity($record, new ManhourEntity());
        }

        return $recordsEntity;
    }

    public function getSummaryOfRecord($employeeId, $date, $employee = null) {

        $record = $this->getRecord($employeeId, $date);

        if ($record == null) {
            $summary = new ManhourSummaryEntity();

            if ($employee == null || !isset($employee->current['timecard']))
                return $summary;

            $summary->timecard = $employee->current['timecard'];
            $summary->employee_id = $employee->id;
            $summary->employeeName = $employee->fullName;
            $summary->departmentId = $employee->current['department']['value'];
            $summary->departmentName = $employee->current['department']['displayName'];

            return $summary;
        }

        $summ = $this->formatSummary($record, $employee);
        return $summ;

    }


    public function getSummaryOfRecordsByDateRange($datefrom, $dateto) {
        $records = $this->getAllRecordsByDateRange($datefrom, $dateto);
        $recordsSummary = array();
        foreach ($records as $record) {
            $recordsSummary[] = $this->formatSummary($record);
        }

        return $recordsSummary;
    }

    private function formatSummary($record, EmployeeEntity $employee = null) {

        $summary = new ManhourSummaryEntity();
        $summary->timecard = $record->timeCard;
        $summary->employee_id = $record->employeeId;
        $summary->employeeName = $record->employeeName;
        $summary->departmentId = $record->department['value'];
        $summary->departmentName = $record->department['displayName'];

        $date = date_create($record->date);
        $otRequest = $this->otRequestService->getApprovedOtRequestByDateRange($record->employeeId, $date, $date);

        if ($employee == null)
            $employee = $this->employeeService->getEmployeeByIdWithStateOnDate($record->employeeId, $date);
            //$employee = $this->employeeService->getEmployeeById($record->employeeId);
        if ($employee == null)
            return;
        $summary->employeeId = $employee->employeeId;

        $summary->date = date_format($date, 'M d Y');

        $history = $this->employeeService->getEmployeeHistoryOnDate($record->employeeId, $date);

        if ($history == null)
            $history = $this->employeeService->getCurrentEmployeeHistory($record->employeeId);

        $summary->timeCard = $history['timecard'];
        $summary->departmentName = $history['department']['displayName'];
        $summary->departmentId = $history['department']['value'];
        $summary->break = isset($history['break']) && $history['break'] != null ? $history['break'] : 0;

        $properHours = 0;
        $recordHours = 0;
        $otHours = 0;
        $ndHours = 0;
        $overtimeCounted = false;
        if ($record->timeIn != null && $record->timeOut != null) {
            // Get employee schedule
            $scheduledTimeIn = isset($employee->current['timein']) ? date_create($employee->current['timein']) : null;
            $scheduledTimeOut = isset($employee->current['timeout']) ? date_create($employee->current['timeout']) : null;
            // Get scheduled time in/out in Time object
            $scheduledTimeIn_ = isset($employee->current['timein']) ? strtotime($employee->current['timein']) : null;
            $scheduledTimeOut_ = isset($employee->current['timeout']) ? strtotime($employee->current['timeout']) : null;
            // Get time in/out in Date object
            $timeIn = date_create($record->timeIn);
            $timeOut = date_create($record->timeOut);
            // Get time in/out in Time object
            $timeIn_ = strtotime($record->timeIn);
            $timeOut_ = strtotime($record->timeOut);
            // Get time to use (either scheduled or actual)
            $properTimeIn = $timeIn_;
            $properTimeOut = $timeOut_;
            if ($scheduledTimeIn_ != null && $timeIn_ <= $scheduledTimeIn_) {
                $properTimeIn = $scheduledTimeIn_;
            }
            if ($timeIn_ < $timeOut_) {
                if ($scheduledTimeOut_ != null && $timeOut_ >= $scheduledTimeOut_) {
                    $properTimeOut = $scheduledTimeOut_;
                }
            } else {
                $properTimeOut = $scheduledTimeOut_;
            }

            // Get time in/out recorded hour
            $x = ($timeOut_ - $timeIn_) / 3600;
            $recordHour = floor($x * 2) / 2;
            $recordHour = $recordHour < 0 ? (24 + $recordHour) : $recordHour;

            // Get scheduled hour
            $x = $scheduledTimeIn_ != null && $scheduledTimeOut_ != null ? ($scheduledTimeOut_ - $scheduledTimeIn_) / 3600 : null;
            $scheduledHour = $x != null ? floor($x * 2) / 2 : null;
            $scheduledHour = $scheduledHour != null && $scheduledHour < 0 ? (24 + $scheduledHour) : $scheduledHour;

            // Get actual hours
            $x = ($properTimeOut - $properTimeIn) / 3600;
            $properHours = floor($x * 2) / 2;
            $properHours = $properHours < 0 ? (24 + $properHours) : $properHours;

            $summary->timeIn = date_format($timeIn, 'H:i');
            $summary->timeOut = date_format($timeOut, 'H:i');
            $summary->undertime = '';
            // If Under time
            if ($scheduledTimeOut != null) {
                if ($scheduledTimeOut > $timeOut && $timeIn < $timeOut) {
                    $summary->timeOut = '';
                    $summary->undertime = date_format($timeOut, 'H:i');
                }
            }

            $otStartTime_;
            $otEndTime_;
            $overtimeCounted = false;
            // Check if OT is counted
            if($otRequest != null) {

                // Get OT start and end time
                $otStartTime_ = strtotime($otRequest[0]->startTime);
                $otEndTime_ = strtotime($otRequest[0]->endTime);


                if (($timeOut_ > $scheduledTimeOut_ && $otStartTime_ < $timeOut_ && $timeIn_ < $timeOut_)
                || ($otStartTime_ > $timeOut_ && $timeIn_ > $timeOut_)) {
                    $overtimeCounted = true;
                    $otHours =  $otRequest[0]->allowedHours;

                    // Get actual hours in OT
                    $otActualHours = ($timeOut_ - $otStartTime_) / 3600;
                    $otActualHours = floor($otActualHours * 2) / 2;
                    $otActualHours = $otActualHours < 0 ? (24 + $otActualHours) : $otActualHours;

                    if ($otActualHours < $otHours) {
                        $otHours = $otActualHours;
                    }

                }
            }

            $totalTimeIn = $properTimeIn;
            $totalTimeOut = $properTimeOut;
            if ($overtimeCounted) {
                $totalTimeOut = $otEndTime_;
            }

            // Check for Night Differential
            $time10PM = strtotime('22:00:00');
            $time4AM = strtotime('04:00:00');
            $ndTimeStart = 0;
            $ndTimeEnd = 0;
            if (
               ($totalTimeOut >= $time10PM && $totalTimeOut <= strtotime('23:59:59')) // if time out is between 10:00pm - 11:59
            || ($totalTimeIn >= $time10PM && $totalTimeIn <= strtotime('23:59:59')) // if time in is between 10:00pm - 11:59pm
            || ($totalTimeOut >= strtotime('00:00:00') && $totalTimeOut < $time4AM) // if time out is between 12:00mn-4:00am
            || ($totalTimeIn >= strtotime('00:00:00') && $totalTimeIn < $time4AM) // if time in is between 12:00mn-4:00am
            || ($totalTimeIn < $time10PM && $properHours > $time10PM - $totalTimeIn) // if time in is near 10:00pm
            )
            {
                // If time in is between 4:00am - 10:00pm, set ND start to 10:00pm
                // otherwise set ND start as time in
                if ($totalTimeIn > $time4AM && $totalTimeIn < $time10PM) {
                    $ndTimeStart = $time10PM;
                }
                else {
                    $ndTimeStart = $totalTimeIn;
                }
                // If time out after 4:00am, set ND end to 4:00am
                // otherwise set ND end as time out
                if ($totalTimeOut > $time4AM && $totalTimeOut < $time10PM) {
                    $ndTimeEnd = $time4AM;
                }
                else {
                    $ndTimeEnd = $totalTimeOut;
                }
                 // Get ND hours
                $x = ($ndTimeEnd - $ndTimeStart) / 3600;
                $ndHours = floor($x * 2) / 2;
                $ndHours = $ndHours < 0 ? (24 + $ndHours) : $ndHours;
            }
        }
        else {
            $summary->timeIn = 'A';
            $summary->timeOut = '';
            $summary->undertime = '';
        }

        $properHours = $properHours > $summary->break ? $properHours - $summary->break : $properHours;
        $summary->regularHours = $properHours;
        $summary->totalHours = $properHours + $otHours;
        $summary->otHours = $otHours;
        $summary->nd = $ndHours;

        $summary->rot = '';
        $summary->sot = '';
        $summary->xsot = '';
        $summary->lhot = '';
        $summary->xlhot = '';
        if ($otRequest != null && $overtimeCounted) {
            if ($otRequest[0]->otType == 'rot') {
                $summary->rot = $otHours;
            }
            else if ($otRequest[0]->otType == 'sot') {
                $summary->sot = $otHours;
            }
            else if ($otRequest[0]->otType == 'xsot') {
                $summary->xsot = $otHours;
            }
            else if ($otRequest[0]->otType == 'lhot') {
                $summary->lhot = $otHours;
            }
            else if ($otRequest[0]->otType == 'xlhot') {
                $summary->xlhot = $otHours;
            }
        }
        $summary->remarks = $record->remarks;
        $summary->outlier = $record->outlier != null ? $record->outlier['displayName'] : null;
        $summary->outlierId = $record->outlier != null ? $record->outlier['value'] : null;
        $summary->authorized = $record->authorized;
        return $summary;
    }
}
