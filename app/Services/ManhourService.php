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
use Carbon\Carbon;

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
        $scheduledHour = 0;
        $otHours = 0;
        $ndHours = 0;
        $overtimeCounted = false;
        if ($record->timeIn != null && $record->timeOut != null) {

            // Get employee schedule
            $scheduledTimeIn = isset($employee->current['timein']) ? $employee->current['timein'] : '';
            $scheduledTimeOut = isset($employee->current['timeout']) ? $employee->current['timeout'] : '';
            $formattedDateTime = null;

            // Special case wherein schedule is 12:00 MN onwards, but employee
            // logged 11:59 or earlier
            if (strtotime($record->timeIn) > strtotime($record->timeOut)
            && strtotime($scheduledTimeIn) < strtotime($scheduledTimeOut)
            // Schedule is earlier than actual time in
            // and scheduled time out is earlier than actual time out
            && strtotime($scheduledTimeIn) < strtotime($record->timeIn)
            && strtotime($scheduledTimeOut) <= strtotime($record->timeOut)) {
                $formattedDateTime = $this->appendDateToTime(Carbon::parse($record->date)->addDay()->format('Y-m-d'), $scheduledTimeIn, $scheduledTimeOut);
            }
            // Normal case
            else {
                $formattedDateTime = $this->appendDateToTime($record->date, $scheduledTimeIn, $scheduledTimeOut);
            }
            $scheduledTimeIn = $formattedDateTime[0];
            $scheduledTimeOut = $formattedDateTime[1];

            // Get scheduled time in/out in Time object
            $scheduledTimeIn_ = $scheduledTimeIn != null ? date_create($scheduledTimeIn) : null;
            $scheduledTimeOut_ = $scheduledTimeOut != null ? date_create($scheduledTimeOut) : null;

            // Format to datetime
            $formattedDateTime = $this->appendDateToTime($record->date, $record->timeIn, $record->timeOut);
            $timeIn_ = $formattedDateTime[0];
            $timeOut_ = $formattedDateTime[1];

            // Get time in/out in Time object
            $timeIn_ = date_create($timeIn_);
            $timeOut_ = date_create($timeOut_);

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
            // $x = ($timeOut_ - $timeIn_) / 3600;
            // $recordHour = floor($x * 2) / 2;
            // $recordHour = $recordHour < 0 ? 0 : $recordHour;
            $x = $timeOut_->diff($timeIn_);
            $recordHour = $this->getTotalHours($x);
            $recordHour = $recordHour < 0 ? 0 : $recordHour;

            // Get scheduled hour
            // $x = $scheduledTimeIn_ != null && $scheduledTimeOut_ != null ? ($scheduledTimeOut_->diff($scheduledTimeIn_)) / 3600 : null;
            // $scheduledHour = $x != null ? floor($x * 2) / 2 : null;
            // $scheduledHour = $scheduledHour != null && $scheduledHour < 0 ? (24 + $scheduledHour) : $scheduledHour;
            $x =  $scheduledTimeIn_ != null && $scheduledTimeOut_ != null ? $scheduledTimeOut_->diff($scheduledTimeIn_) : null;
            $scheduledHour = $x != null ? $this->getTotalHours($x) : null;
            $scheduledHour = $scheduledHour != null && $scheduledHour < 0 ? 0 : $scheduledHour;

            // Get actual hours
            // $x = ($properTimeOut - $properTimeIn) / 3600;
            // $properHours = floor($x * 2) / 2;
            // $properHours = $properHours < 0 ? 0 : $properHours;
            $x = $properTimeOut->diff($properTimeIn);
            $properHours = $this->getTotalHours($x);
            $properHours = $properHours < 0 ? 0 : $properHours;


            if (isset($record->outlier['details']) && $record->outlier['details'] === 'payable') {
                $properHours = 8;
            }

            // Get time in/out in Date object
            $timeIn = date_create($record->timeIn);
            $timeOut = date_create($record->timeOut);

            $summary->timeIn = date_format($timeIn, 'H:i');
            $summary->timeOut = date_format($timeOut, 'H:i');
            $summary->undertime = '';
            // If Under time
            if ($scheduledTimeOut_ != null) {
                if ($scheduledTimeOut_ > $timeOut_ && $timeIn_ < $timeOut_) {
                    $summary->timeOut = '';
                    $summary->undertime = date_format($timeOut_, 'H:i');
                }
            }

            $otStartTime_;
            $otEndTime_;
            $overtimeCounted = false;
            // Check if OT is counted
            if($otRequest != null) {

                // Format to datetime
                $formattedDateTime = $this->appendDateToTime($record->date, $otRequest[0]->startTime, $otRequest[0]->endTime);
                $otStartTime_ = $formattedDateTime[0];
                $otEndTime_ = $formattedDateTime[1];
                // Get OT start and end time
                $otStartTime_ = date_create($otStartTime_);
                $otEndTime_ = date_create($otEndTime_);

                // If scheduled time out is earlier, and actual time out is earlier than OT start time, and actual time in is earlier than actual time out
                // or actual time out is earlier than OT start time, and time out is the next day today
                // if (($timeOut_ > $scheduledTimeOut_ && $otStartTime_ <= $timeOut_ && $timeIn_ < $timeOut_)
                // || ($otStartTime_ > $timeOut_ && $timeIn_ > $timeOut_)) {
                if ($timeIn_ <= $otStartTime_ && $timeOut_ >= $otEndTime_
                   || $timeIn_ >= $otStartTime_ && $timeOut_ >= $otEndTime_ && $timeIn_ < $otEndTime_
                   || $timeIn_ <= $otStartTime_ && $timeOut_ <= $otEndTime_ && $timeOut > $otStartTime_) {

                    $overtimeCounted = true;
                    $otHours =  $otRequest[0]->allowedHours;

                    if ($timeIn_ > $otStartTime_) {
                        $otStartTime_ = $timeIn_;
                    }

                    if ($timeOut_ < $otEndTime_) {
                        $otEndTime_ = $timeOut_;
                    }

                    // Get actual hours in OT
                    // $otActualHours = ($timeOut_ - $otStartTime_) / 3600;
                    // $otActualHours = floor($otActualHours * 2) / 2;
                    // $otActualHours = $otActualHours < 0 ? 0 : $otActualHours;
                    $x = $timeOut_->diff($otStartTime_);
                    $otActualHours = $this->getTotalHours($x);
                    $otActualHours = $otActualHours < 0 ? 0 : $otActualHours;

                    if ($otActualHours < $otHours) {
                        $otHours = $otActualHours;
                    }

                }
            }

            $totalTimeIn = $properTimeIn;
            $totalTimeOut = $properTimeOut;
            if ($overtimeCounted && $totalTimeOut < $otEndTime_) {
                $totalTimeOut = $otEndTime_;
            }

            // Format to datetime
            $formattedDateTime = $this->appendDateToTime($record->date, '22:00:00', '04:00:00');
            $time10PM = $formattedDateTime[0];
            $time4AM = $formattedDateTime[1];
            // Check for Night Differential
            $time10PM = date_create($time10PM);
            $time4AM = date_create($time4AM);
            $time4AM_Today= date_create($record->date.' 04:00:00');
            $ndTimeStart = $time10PM;
            $ndTimeEnd = $time10PM;

            // If time in is beyond 12:00 MN to 4:00 AM and time out is past 4:00 AM
            if ($totalTimeIn <= $time4AM_Today && $totalTimeOut >= $time4AM_Today) {
                $ndTimeStart = $totalTimeIn;
                $ndTimeEnd = $time4AM_Today;
            }
            // If time in is beyond 12:00 MN to 4:00 AM and time out is before 4:00 AM
            else if ($totalTimeIn <= $time4AM_Today && $totalTimeOut <= $time4AM_Today) {
                $ndTimeStart = $totalTimeIn;
                $ndTimeEnd = $totalTimeOut;
            }
            // If time in is before 10:00 PM and time out is past 4:00 AM
            else if ($totalTimeIn <= $time10PM && $totalTimeOut >= $time4AM) {
                $ndTimeStart = $time10PM;
                $ndTimeEnd = $time4AM;
            }
            // If time in is past 10:00 PM and time out is past 4:00 AM
            else if ($totalTimeIn >= $time10PM && $totalTimeOut >= $time4AM) {
                $ndTimeStart = $totalTimeIn;
                $ndTimeEnd = $time4AM;
            }
            // If time in is past 10:00 PM and time out is before 4:00 AM
            else if ($totalTimeIn >= $time10PM  && $totalTimeOut <= $time4AM) {
                $ndTimeStart = $totalTimeIn;
                $ndTimeEnd = $totalTimeOut;
            }
            // If time in is before 10:00 PM and time out is between 10:00 PM and 4:00 AM
            else if ($totalTimeIn <= $time10PM  && $totalTimeOut <= $time4AM && $totalTimeOut >= $time10PM) {
                $ndTimeStart = $time10PM;
                $ndTimeEnd = $totalTimeOut;
            }
            // Get ND hours
            // $x = ($ndTimeEnd - $ndTimeStart) / 3600;
            // $ndHours = floor($x * 2) / 2;
            // $ndHours = $ndHours < 0 ? 0 : $ndHours;
            $x = $ndTimeEnd->diff($ndTimeStart);
            $ndHours = $this->getTotalHours($x);
            $ndHours = $ndHours <= 0 ? '' : $ndHours;

        }
        else {
            $summary->timeIn = 'A';
            $summary->timeOut = '';
            $summary->undertime = '';
        }

        $break = $summary->break;
        // If work hours is less than or half the required work hours,
        // Do not count breaks
        if ($properHours <= $scheduledHour/2) {
            $break = 0;
        }

        // If sunday
        if (date('N', strtotime($record->date)) > 6) {
            $properHours = 0;
        }

        $properHours = $properHours > $break ? $properHours - $break : $properHours;
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


    private function appendDateToTime($date, $time1, $time2) {

        if ($time1 === null || $time2 === null)
            return [null, null];

        $dateFormatted = Carbon::parse($date);

        if ($date === null)
            return [null, null];

        $returnDateTime1 = '';
        $returnDateTime2 = '';

        $dateFormatted->addDay();
        $dateTomorrow = $dateFormatted->format('Y-m-d');

        $time1Formatted = strtotime($time1);
        $time2Formatted = strtotime($time2);

        if ($time1Formatted > $time2Formatted) {
            return [ $date.' '.$time1, $dateTomorrow.' '.$time2 ];
        }

        return [ $date.' '.$time1, $date.' '.$time2 ];

    }


    private function getTotalHours(\DateInterval $int){

        $hours = ($int->d * 24) + $int->h + $int->i / 60;
        return floor($hours * 2) / 2;

    }
}
