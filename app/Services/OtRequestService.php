<?php

namespace App\Services;

use App\Contracts\IOtRequestService;
use App\Entities\OtRequestEntity;
use App\Models\OtRequest;

class OtRequestService extends EntityService implements IOtRequestService {


    public function getOtRequests() {

        $otRequests = OtRequest::all();
        if ($otRequests == null) return null;

        $otReq = array();
        foreach ($otRequests as $otRequest) {
            $otReq[] = $this->mapToEntity($otRequest, new OtRequestEntity());
        }

        return $otReq;
    }

    public function getPendingOtRequests() {

        $otRequests = OtRequest::whereNull('approval')->get();
        if ($otRequests == null) return null;

        $otReq = array();
        foreach ($otRequests as $otRequest) {
            $otReq[] = $this->mapToEntity($otRequest, new OtRequestEntity());
        }

        return $otReq;
    }

    public function getPendingOtRequestsByDateRange($datefrom, $dateto) {

        $otRequests = OtRequest::whereNull('approval')->whereBetween('otDate', [$datefrom, $dateto])->get();
        if ($otRequests == null) return null;

        $otReq = array();
        foreach ($otRequests as $otRequest) {
            $otReq[] = $this->mapToEntity($otRequest, new OtRequestEntity());
        }

        return $otReq;
    }

    public function getApprovedOtRequests() {

        $otRequests = OtRequest::where('approval', true)->get();
        if ($otRequests == null) return null;

        $otReq = array();
        foreach ($otRequests as $otRequest) {
            $otReq[] = $this->mapToEntity($otRequest, new OtRequestEntity());
        }

        return $otReq;
    }

    public function getApprovedOtRequestsByDateRange($datefrom, $dateto) {

        $otRequests = OtRequest::where('approval', true)->whereBetween('otDate', [$datefrom, $dateto])->get();
        if ($otRequests == null) return null;

        $otReq = array();
        foreach ($otRequests as $otRequest) {
            $otReq[] = $this->mapToEntity($otRequest, new OtRequestEntity());
        }

        return $otReq;
    }

    public function getApprovedOtRequestByDateRange($employeeId, $datefrom, $dateto) {

        $otRequests = OtRequest::where('approval', true)->where('employee_id', $employeeId)->whereBetween('otDate', [$datefrom, $dateto])->get();
        if ($otRequests == null) return null;

        $otReq = array();
        foreach ($otRequests as $otRequest) {
            $otReq[] = $this->mapToEntity($otRequest, new OtRequestEntity());
        }

        return $otReq;
    }

    public function getDeniedOtRequestsByDateRange($datefrom, $dateto) {

        $otRequests = OtRequest::where('approval', false)->whereBetween('otDate', [$datefrom, $dateto])->get();
        if ($otRequests == null) return null;

        $otReq = array();
        foreach ($otRequests as $otRequest) {
            $otReq[] = $this->mapToEntity($otRequest, new OtRequestEntity());
        }

        return $otReq;
    }

    public function addOtRequest(OtRequestEntity $entity) {

        //$otRequest = OtRequest::where('otDate', $entity->otDate)->where('employee_id', $entity->employeeId)->first();

//        if ($otRequest == null) {
//            $otRequest = new OtRequest();
//            $otRequest->otDate = $entity->otDate;
//            $otRequest->employee_id = $entity->employeeId;
//        }

        //
        $otRequest = new OtRequest();
        $otRequest->otDate = $entity->otDate;
        $otRequest->employee_id = $entity->employeeId;

        $otRequest->employeeName = $entity->employeeName;
        $otRequest->department = $entity->department;
        $otRequest->startTime = $entity->startTime;
        $otRequest->endTime = $entity->endTime;
        $otRequest->allowedHours = $entity->allowedHours;
        $otRequest->reason = $entity->reason;
        $otRequest->otType = $entity->otType;
        $otRequest->approval = $entity->approval === '' ? null : $entity->approval;

        try {
            $otRequest->save();
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

    public function getOtRequestOfEmployee($employeeId, $date) {

        $otRequest = OtRequest::where('employee_id', $employeeId)->where('otDate', $date)->first();

        if ($otRequest == null) return null;

        return $this->mapToEntity($otRequest, new OtRequestEntity());

    }

    public function approveOtRequest($id) {
        return $this->setApproval($id, true);
    }

    public function declineOtRequest($id) {
        return $this->setApproval($id, false);
    }

    private function setApproval($id, $approval) {

        $otRequest = OtRequest::find($id);

        if ($otRequest == null) return ['result' => false, 'message' => 'OT request record cannot be found'];

        $otRequest->approval = $approval;

        try {
            $otRequest->save();
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

    protected function mapToEntity($model, $entity) {

        $entity = parent::mapToEntity($model, $entity);
        $entity->otDate = $model->otDate;
        $entity->employeeId = $model->employee_id;
        $entity->employeeName = $model->employeeName;
        $entity->allowedHours = $model->allowedHours;
        $entity->startTime = $model->startTime;
        $entity->endTime = $model->endTime;
        $entity->reason = $model->reason != null ? stripslashes($model->reason) : null;
        $entity->approval = $model->approval;
        $entity->otType = $model->otType;
        $dept = $model->departmentDetails;

        $entity->department = [
            'value' => $model->department,
            'displayName' => $dept != null ? $dept->value : ''
        ];

        return $entity;

    }
}
