<?php

namespace App\Services;

use App\Contracts\IManhourService;
use App\Contracts\IOTRequestService;
use App\Contracts\IEmployeeService;
use App\Contracts\ICategoryService;
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

        $record = Manhour::where('recordDate', $entity->date)->where('employee_id', $entity->employeeId)->first();

        if ($record == null) {
            $record = new Manhour();
            $record->recordDate = $entity->date;
            $record->employee_id = $entity->employeeId;
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
        $entity->department = [
            'value' => $model->department,
            'displayName' => $model->departmentDetails->value
        ];

        $entity->authorized = $model->authorized;
        $entity->outlier = $model->outlier;
        $entity->remarks = stripslashes($model->remarks);

        return $entity;

    }


    public function getRecord($id, $date) {

        $record = Manhour::where('employee_id', $id)->where('recordDate', $date)->first();

        if ($record == null)
            return null;

        return $this->mapToEntity($record, new ManhourEntity());

    }


    public function getSummaryOfRecord($employeeId, $date) {

        $record = $this->getRecord($employeeId, $date);

        if ($record == null) return null;

        return $this->formatSummary($record);

    }


    public function getSummaryOfRecordsByDateRange($datefrom, $dateto) {
        $records = $this->getAllRecordsByDateRange($datefrom, $dateto);
        $recordsSummary = array();
        foreach ($records as $record) {
            $recordsSummary[date_format(date_create($record->date), 'j')] = $this->formatSummary($record);
        }

        return $recordsSummary;
    }

    private function formatSummary($record) {

        $summary = new ManhourSummaryEntity();
        $summary->timecard = $record->timeCard;
        $summary->employee_id = $record->employeeId;
        $summary->employeeName = $record->employeeName;
        $summary->departmentId = $record->department['value'];
        $summary->departmentName = $record->department['displayName'];

        $date = date_create($record->date);
        $employee = $this->employeeService->getEmployeeById($record->employeeId);
        $otRequest = $this->otRequestService->getApprovedOtRequestByDateRange($record->employeeId, $date, $date);

        if ($employee == null)
            return;
        $summary->employeeId = $employee->employeeId;
        $summary->date = date_format($date, 'M d Y');
        $actualHours = 0;
        $otHours = 0;
        $overtimeCounted = false;
        if ($record->timeIn != null && $record->timeOut != null) {
            // Get employee schedule
            $scheduledTimeIn = key_exists('timein', $employee->details) ? date_create($employee->details['timein']['value']) : null;
            $scheduledTimeOut = key_exists('timeout', $employee->details) ? date_create($employee->details['timeout']['value']) : null;
            // Get scheduled time in/out in Time object
            $scheduledTimeIn_ = key_exists('timein', $employee->details) ? strtotime($employee->details['timein']['value']) : null;
            $scheduledTimeOut_ =  key_exists('timeout', $employee->details) ? strtotime($employee->details['timeout']['value']) : null;
            // Get time in/out in Date object
            $timeIn = date_create($record->timeIn);
            $timeOut = date_create($record->timeOut);
            // Get time in/out in Time object
            $timeIn_ = strtotime($record->timeIn);
            $timeOut_ = strtotime($record->timeOut);
            // Get time to use (either scheduled or actual)
            $actualTimeIn = $timeIn_;
            $actualTimeOut = $timeOut_;
            if ($scheduledTimeIn_ != null && $timeIn_ <= $scheduledTimeIn_) {
                $actualTimeIn = $scheduledTimeIn_;
            }
            if ($scheduledTimeOut_ != null && $timeOut_ >= $scheduledTimeOut_) {
                $actualTimeOut = $scheduledTimeOut_;
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
            $x = ($actualTimeOut - $actualTimeIn) / 3600;
            $actualHours = floor($x * 2) / 2;
            $actualHours = $actualHours < 0 ? (24 + $actualHours) : $actualHours;

            $summary->timeIn = date_format($timeIn, 'h:i A');
            $summary->timeOut = date_format($timeOut, 'h:i A');
            $summary->undertime = '';
            // If Under time
            if ($scheduledTimeOut != null) {
                if ($scheduledTimeOut > $timeOut) {
                    $summary->timeOut = '';
                    $summary->undertime = date_format($timeOut, 'h:i A');
                }
            }

            // Check if OT is counted
            if($otRequest != null) {

                // Get OT start and end time
                $otStartTime_ = strtotime($otRequest[0]->startTime);
                $otEndTime_ = strtotime($otRequest[0]->endTime);

                if ($timeOut_ > $scheduledTimeOut_ && $otStartTime_ < $timeOut_) {
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
        }
        else {
            $summary->timeIn = 'A';
            $summary->timeOut = '';
            $summary->undertime = '';
        }

        $summary->hours = $actualHours;

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

        return $summary;
    }
}
