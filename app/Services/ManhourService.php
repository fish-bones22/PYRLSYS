<?php

namespace App\Services;

use App\Contracts\IManhourService;
use App\Contracts\IOTRequestService;
use App\Contracts\IEmployeeService;
use App\Contracts\ICategoryService;
use App\Entities\EmployeeEntity;
use App\Entities\ManhourSummaryEntity;
use App\Entities\ManhourEntity;
use App\Entities\OtRequestEntity;
use App\Models\Manhour;
use App\Models\Holiday;
use Carbon\Carbon;
use PHPUnit\Framework\Exception;

class ManhourService extends EntityService implements IManhourService
{

    private $deptName_15minRule = ["admin", "administration", "administrator"];
    private $deptName_12hoursExtensionRule = ["security"];

    private $otRequestService;
    private $employeeService;
    private $categoryService;

    public function __construct(IOtRequestService $otRequestService, IEmployeeService $employeeService, ICategoryService $categoryService)
    {
        $this->otRequestService = $otRequestService;
        $this->employeeService = $employeeService;
        $this->categoryService = $categoryService;
    }

    public function getAllRecords()
    {
        $records = Manhour::all();
        if ($records == null) return null;
        $recordsEntity = array();
        foreach ($records as $record) {
            $recordsEntity[] = $this->mapToEntity($record, new ManhourEntity());
        }

        return $recordsEntity;
    }


    public function getAllRecordsByDateRange($datefrom, $dateto)
    {

        $records = Manhour::whereBetween('recordDate', [$datefrom, $dateto])->get();

        if ($records == null) return null;

        $recordsEntity = array();
        foreach ($records as $record) {
            $recordsEntity[] = $this->mapToEntity($record, new ManhourEntity());
        }

        return $recordsEntity;
    }


    public function recordManhour(ManhourEntity $entity, $otApproval = null)
    {

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


        if ($entity->timeIn === null && $entity->timeOut === null) {
            return [
                'result' => true
            ];
        }

        // Auto OT count
        // $timeFormatted_ = $this->appendDateToTime($entity->date, $entity->timeIn, $entity->timeOut);
        // $timeIn_ = date_create($timeFormatted_[0]);
        // $timeOut_ = date_create($timeFormatted_[1]);
        // $date = date_create($entity->date);

        // $timeTable = $this->employeeService->getEmployeeTimeTable($entity->employee_id, $date);
        // $scheduledTimeIn = $timeTable['timein'];
        // $scheduledTimeOut = $timeTable['timeout'];
        // $scheduledTimeFormatted_ = $this->appendDateToTime($entity->date, $scheduledTimeIn, $scheduledTimeOut);
        // $scheduledTimeIn_ = date_create($scheduledTimeFormatted_[0]);
        // $scheduledTimeOut_ = date_create($scheduledTimeFormatted_[1]);

        // $offsetHour = 0;
        // $earlyOt = false;

        // // Check for OTs
        // if ($timeIn_ < $scheduledTimeIn_) {
        //     $x = $scheduledTimeIn_->diff($timeIn_);
        //     $offsetHour = $this->getTotalHours($x);
        //     $offsetHour = $offsetHour < 0 ? 0 : $offsetHour;
        //     $earlyOt = true;
        // }
        // if ($timeOut_ > $scheduledTimeOut_) {
        //     $x = $timeOut_->diff($scheduledTimeOut_);
        //     $offsetHour = $this->getTotalHours($x);
        //     $offsetHour = $offsetHour < 0 ? 0 : $offsetHour;
        //     $earlyOt = false;
        // }


        // if ($offsetHour >= 1) {

        //     $otTrimmedOffset = floor($offsetHour);
        //     $otStartTime = null;
        //     $otEndTime = null;

        //     if ($earlyOt) {
        //         $otStartTime = date("H:i:s", strtotime('-' . $otTrimmedOffset . ' hours', strtotime($scheduledTimeFormatted_[0])));
        //         $otEndTime = date_format($scheduledTimeIn_, 'H:i:s');
        //     } else {
        //         $otStartTime = date_format($scheduledTimeOut_, 'H:i:s');
        //         $otEndTime = date("H:i:s", strtotime('+' . $otTrimmedOffset . ' hours', strtotime($scheduledTimeFormatted_[1])));
        //     }

        //     $otType = 'rot';
        //     if ($this->isSunday($entity->date)) {
        //         $otType = 'sot';
        //     } else {
        //         $holiday = $this->getHoliday($entity->date);
        //         if ($holiday != null && $holiday['type'] == 'legal') {
        //             $otType = 'lhot';
        //         }
        //     }

        //     $otReq = new OtRequestEntity();
        //     $otReq->otDate = $date;
        //     $otReq->employeeId = $entity->employee_id;
        //     $otReq->employeeName = $entity->employeeName;
        //     $otReq->department = $entity->department;
        //     $otReq->startTime = $otStartTime;
        //     $otReq->endTime = $otEndTime;
        //     $otReq->allowedHours = $otTrimmedOffset;
        //     $otReq->reason = 'System generated';
        //     $otReq->otType = $otType;
        //     $otReq->approval = $otApproval;
        //     $result = $this->otRequestService->addOtRequest($otReq);

        //     if (!$result['result']) return $result;
        // }

        return [
            'result' => true
        ];
    }

    protected function mapToEntity($model, $entity)
    {

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
                'displayName' => $model->outlierDetails->value,
                'details' => $model->outlierDetails->detail
            ];
        $entity->remarks = stripslashes($model->remarks);

        return $entity;
    }


    public function getRecord($id, $date)
    {

        $record = Manhour::where('employee_id', $id)->where('recordDate', $date)->first();

        if ($record == null)
            return null;

        return $this->mapToEntity($record, new ManhourEntity());
    }

    public function getOutliersOnDateRange($employeeId, $datefrom, $dateto)
    {
        $records = Manhour::where('employee_id', $employeeId)->whereBetween('recordDate', [$datefrom, $dateto])->get();

        if ($records == null) return null;

        $recordsEntity = array();
        foreach ($records as $record) {
            $recordsEntity[] = $this->mapToEntity($record, new ManhourEntity());
        }

        return $recordsEntity;
    }

    public function getSummaryOfRecord($employeeId, $date, $employee = null)
    {
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

            // Check for holiday
            if ($this->getHoliday($date) == null) {
                return $summary;
            }

            $history = $this->employeeService->getEmployeeHistoryOnDate($employeeId, date_create($date));
            $timeTable = $this->employeeService->getEmployeeTimeTable($employeeId, date_create($date));

            if ($history == null)
                $history = $this->employeeService->getCurrentEmployeeHistory($employee->employeeId);

            $summary->date = $date;
            $summary->timeCard = $history['timecard'];
            $summary->departmentName = $history['department']['displayName'];
            $summary->departmentId = $history['department']['value'];
            $summary->break = isset($timeTable['break']) && $timeTable['break'] != null ? $timeTable['break'] : 0;

            $properHours = 0;
            $recordHours = 0;
            $scheduledHour = 0;
            $otHours = 0;
            $ndHours = 0;
            $isLate = false;
            $overtimeCounted = false;

            // Get employee schedule
            $scheduledTimeIn = isset($timeTable['timein']) ? $timeTable['timein'] : null;
            $scheduledTimeOut = isset($timeTable['timeout']) ? $timeTable['timeout'] : null;
            $formattedDateTime = $this->appendDateToTime($date, $scheduledTimeIn, $scheduledTimeOut);
            $scheduledTimeIn = $formattedDateTime[0];
            $scheduledTimeOut = $formattedDateTime[1];

            // Get scheduled time in/out in Time object
            $scheduledTimeIn_ = $scheduledTimeIn != null ? date_create($scheduledTimeIn) : null;
            $scheduledTimeOut_ = $scheduledTimeOut != null ? date_create($scheduledTimeOut) : null;
            // Get scheduled hour
            $x =  $scheduledTimeIn_ != null && $scheduledTimeOut_ != null ? $scheduledTimeOut_->diff($scheduledTimeIn_) : null;
            $scheduledHour = $x != null ? $this->getTotalHours($x) : null;
            $scheduledHour = $scheduledHour != null && $scheduledHour < 0 ? 0 : $scheduledHour;

            $summary->isHoliday = true;
            $summary->totalPayableHours = $scheduledHour - $summary->break;

            return $summary;
        }

        $summ = $this->formatSummary($record, $employee);
        return $summ;
    }


    public function getSummaryOfRecordsByDateRange($datefrom, $dateto)
    {
        $records = $this->getAllRecordsByDateRange($datefrom, $dateto);
        $recordsSummary = array();
        foreach ($records as $record) {
            $recordsSummary[] = $this->formatSummary($record);
        }

        return $recordsSummary;
    }


    public function saveHoliday($entity)
    {

        if (!isset($entity['date']))
            return [
                'result' => false,
                'message' => 'Date is not specified'
            ];
        if (!isset($entity['name']))
            return [
                'result' => false,
                'message' => 'Name is not specified'
            ];
        if (!isset($entity['type']))
            return [
                'result' => false,
                'message' => 'Type is not specified'
            ];

        $model = Holiday::where('holidayDate', date_create($entity['date']))->first();
        if ($model === null) {
            $model = new Holiday();
            $model->holidayDate = date_format(date_create($entity['date']), 'Y-m-d');
        }
        $model->name = $entity['name'];
        $model->description = $entity['description'];
        $model->type = $entity['type'];

        try {
            $model->save();
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


    public function deleteHoliday($date)
    {

        if ($date === null || $date === '')
            return [
                'result' => false,
                'message' => 'Date is not specified'
            ];

        $holiday = Holiday::where('holidayDate', date_create($date))->first();
        if ($holiday == null) {
            return [
                'result' => false,
                'message' => 'No holiday on date specified'
            ];
        }

        try {
            $holiday->delete();
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


    public function getHoliday($date)
    {
        $holiday = Holiday::where('holidayDate', date_create($date))->first();
        if ($holiday == null) return null;

        return [
            'name' => $holiday->name,
            'description' => $holiday->description,
            'date' => date_format(date_create($date), 'Y-m-d'),
            'type' => $holiday->type
        ];
    }


    public function getHolidays($dateFrom, $dateTo)
    {

        $holidays = Holiday::whereBetween('holidayDate', [date_create($dateFrom), date_create($dateTo)])->orderBy('holidayDate')->get();

        if ($holidays == null) return null;

        $holidaysRecord = [];
        foreach ($holidays as $holiday) {
            $holidaysRecord[] = [
                'name' => $holiday->name,
                'description' => $holiday->description,
                'date' => date_format(date_create($holiday->holidayDate), 'Y-m-d'),
                'type' => $holiday->type
            ];
        }

        return $holidaysRecord;
    }


    private function formatSummary($record, EmployeeEntity $employee = null)
    {

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
        $timeTable = $this->employeeService->getEmployeeTimeTable($record->employeeId, $date);

        if ($history == null)
            $history = $this->employeeService->getCurrentEmployeeHistory($record->employeeId);

        $summary->timeCard = $history['timecard'];
        $summary->departmentName = $history['department']['displayName'];
        $summary->departmentId = $history['department']['value'];
        $summary->break = isset($timeTable['break']) && $timeTable['break'] != null ? $timeTable['break'] : 0;

        $properHours = 0;
        $recordHours = 0;
        $scheduledHour = 0;
        $otHours = 0;
        $ndHours = 0;
        $isLate = false;
        $overtimeCounted = false;
        if ($record->timeIn != null && $record->timeOut != null || isset($record->outlier['details'])) {

            // Get employee schedule
            $scheduledTimeIn = isset($timeTable['timein']) ? $timeTable['timein'] : null;
            $scheduledTimeOut = isset($timeTable['timeout']) ? $timeTable['timeout'] : null;
            $formattedDateTime = null;


            // Special case wherein schedule is 12:00 MN onwards, but employee
            // logged 11:59 or earlier
            if (
                strtotime($record->timeIn) > strtotime($record->timeOut)
                && strtotime($scheduledTimeIn) < strtotime($scheduledTimeOut)
                // Schedule is earlier than actual time in
                // and scheduled time out is earlier than actual time out
                && strtotime($scheduledTimeIn) < strtotime($record->timeIn)
                && strtotime($scheduledTimeOut) <= strtotime($record->timeOut)
            ) {
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
            $x = $timeOut_->diff($timeIn_);
            $recordHour = $this->getTotalHours($x);
            $recordHour = $recordHour < 0 ? 0 : $recordHour;

            // Get scheduled hour
            $x =  $scheduledTimeIn_ != null && $scheduledTimeOut_ != null ? $scheduledTimeOut_->diff($scheduledTimeIn_) : null;
            $scheduledHour = $x != null ? $this->getTotalHours($x) : null;
            $scheduledHour = $scheduledHour != null && $scheduledHour < 0 ? 0 : $scheduledHour;

            //** For 12 hour extension rule */
            if ($recordHour > $scheduledHour && in_array(strtolower($summary->departmentName), $this->deptName_12hoursExtensionRule)) {
                $properTimeOut = $timeOut_;
                // Get hours from scheduled time in (to not account for tardiness) to time out
                $x =  $scheduledTimeIn_ != null && $properTimeOut != null ? $properTimeOut->diff($scheduledTimeIn_) : null;
                $hoursSpent = $x != null ? $this->getTotalHours($x) : null;
                $hoursSpent = $hoursSpent != null && $hoursSpent < 0 ? 0 : $hoursSpent;
                var_dump($hoursSpent);
                // Limit to 12 hours only
                if ($hoursSpent > 12) {
                    $properTimeOut = date_create(date('Y-m-d H:i:s',strtotime('+4 hours', strtotime($scheduledTimeOut))));
                }
            }

            // All else fails
            if ($scheduledTimeIn_ === null) {
                return $summary;
            }

            //Check if late
            // 15 mins grace period is applicable only to
            // department with names defined in $deptName_15minRule
            $minPassed = $timeIn_->diff($scheduledTimeIn_);
            var_dump($this->getTotalHoursNotFloored($minPassed));
            if (in_array(strtolower($summary->departmentName), $this->deptName_15minRule)) {
                if ($this->getTotalHoursNotFloored($minPassed) > 0.25) {
                    $isLate = true;
                }
            }
            // No grace period
            else if ($this->getTotalHoursNotFloored($minPassed) > 0) {
                $isLate = true;
            }
            // Get actual hours
            $x = $properTimeOut->diff($properTimeIn);
            if (!$isLate) {
                $x = $properTimeOut->diff($scheduledTimeIn_);
            }
            $properHours = $this->getTotalHours($x);
            $properHours = $properHours < 0 ? 0 : $properHours;
            var_dump($properTimeOut);

            // If leave
            if (isset($record->outlier['details']) && $record->outlier['details'] === 'payable') {
                $properHours =  $scheduledHour;
                $summary->isExcused = true;
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
            if ($otRequest != null) {

                // Format to datetime
                $formattedDateTime = $this->appendDateToTime($record->date, $otRequest[0]->startTime, $otRequest[0]->endTime);
                $otStartTime_ = $formattedDateTime[0];
                $otEndTime_ = $formattedDateTime[1];
                // Get OT start and end time
                $otStartTime_ = date_create($otStartTime_);
                $otEndTime_ = date_create($otEndTime_);


                $isEarlyOt = false;
                // Check if OT is early OT (offset) or reg OT (after regular hours)
                if ($scheduledTimeIn_ >= $otStartTime_ && $scheduledTimeIn_ >= $otEndTime_) {
                    $isEarlyOt = true;
                }

                // Regular OT
                if (
                    !$isEarlyOt
                    && (
                        // Time out is later than OT end time
                        // or actual in is late for OT but actual in is earlier than OT out and actual out is later than OT out (incomplete OT)
                        // or actual in is earlier for OT in but actual out is earlier than OT out  (incomplete OT)
                        $timeIn_ <= $otStartTime_ && $timeOut_ >= $otEndTime_
                        || $timeIn_ >= $otStartTime_ && $timeOut_ >= $otEndTime_ && $timeIn_ < $otEndTime_
                        || $timeIn_ <= $otStartTime_ && $timeOut_ <= $otEndTime_ && $timeOut_ > $otStartTime_)
                ) {

                    $overtimeCounted = true;
                    $otHours =  $otRequest[0]->allowedHours;

                    if ($timeIn_ > $otStartTime_) {
                        $otStartTime_ = $timeIn_;
                    }

                    if ($timeOut_ < $otEndTime_) {
                        $otEndTime_ = $timeOut_;
                    }

                    // Get actual hours in OT
                    $x = $timeOut_->diff($otStartTime_);
                    $otActualHours = $this->getTotalHours($x);
                    $otActualHours = $otActualHours < 0 ? 0 : $otActualHours;

                    if ($otActualHours < $otHours) {
                        $otHours = $otActualHours;
                    }
                }
                // Early OT (offset)
                else if (
                    $isEarlyOt
                    && (
                        // actual time in is earlier than OT start and actual out is later than OT end
                        // or actual time in it later then OT start but actual out is later than OT end (incomplete OT)
                        // or actual time in is earlier than OT start but actual time out is earlier than OT end (incomplete OT)
                        $timeIn_ <= $otStartTime_ && $timeOut >= $otEndTime_
                        || $timeIn_ >= $otStartTime_ && $timeOut >= $otEndTime_
                        || $timeIn_ <= $otStartTime_ && $timeOut <= $otEndTime_)
                ) {

                    $overtimeCounted = true;
                    $otHours =  $otRequest[0]->allowedHours;

                    if ($timeIn_ < $otStartTime_) {
                        $otStartTime_ = $timeIn_;
                    }

                    if ($timeOut_ > $otEndTime_) {
                        $otEndTime_ = $timeOut_;
                    }

                    // Get actual hours in OT
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
            $time4AM_Today = date_create($record->date . ' 04:00:00');
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
            $x = $ndTimeEnd->diff($ndTimeStart);
            $ndHours = $this->getTotalHours($x);
            $ndHours = $ndHours <= 0 ? '' : $ndHours;
        } else {
            $summary->timeIn = 'A';
            $summary->timeOut = '';
            $summary->undertime = '';
        }

        $break = $summary->break;
        // If work hours is less than or half the required work hours,
        // Do not count breaks
        if ($properHours <= $scheduledHour / 2) {
            $break = 0;
        }

        // If sunday
        if (date('N', strtotime($record->date)) > 6) {
            $properHours = 0;
        }

        $properHours = $properHours > $break ? $properHours - $break : $properHours;
        $summary->regularHours = $properHours;
        $summary->totalPayableHours = $properHours;
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
            } else if ($otRequest[0]->otType == 'sot') {
                $summary->sot = $otHours;
            } else if ($otRequest[0]->otType == 'xsot') {
                $summary->xsot = $otHours;
            } else if ($otRequest[0]->otType == 'lhot') {
                $summary->lhot = $otHours;
            } else if ($otRequest[0]->otType == 'xlhot') {
                $summary->xlhot = $otHours;
            }
        }
        $summary->remarks = $record->remarks;
        $summary->outlier = $record->outlier != null ? $record->outlier['displayName'] : null;
        $summary->outlierId = $record->outlier != null ? $record->outlier['value'] : null;
        $summary->authorized = $record->authorized;
        return $summary;
    }


    private function appendDateToTime($date, $time1, $time2)
    {

        if ($time1 === null || $time2 === null)
            return [null, null];

        $time1 = date_format(date_create($time1), 'H:i');
        $time2 = date_format(date_create($time2), 'H:i');

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
            return [$date . ' ' . $time1, $dateTomorrow . ' ' . $time2];
        }

        return [$date . ' ' . $time1, $date . ' ' . $time2];
    }


    private function getTotalHours(\DateInterval $int)
    {

        $hours = ($int->d * 24) + $int->h + $int->i / 60;
        return floor($hours * 2) / 2;
    }

    private function getTotalHoursNotFloored(\DateInterval $int)
    {

        $hours = ($int->d * 24) + $int->h + $int->i / 60;
        return $hours;
    }

    private function isSunday($date)
    {
        return date('N', strtotime($date)) > 6;
    }
}
