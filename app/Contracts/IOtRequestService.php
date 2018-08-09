<?php

namespace App\Contracts;

use App\Entities\OtRequestEntity;

interface IOtRequestService {

    public function getOtRequests();
    public function getPendingOtRequests();
    public function getApprovedOtRequests();
    public function getApprovedOtRequestByDateRange($employeeId, $datefrom, $dateto);
    public function addOtRequest(OtRequestEntity $entity);
    public function getOtRequestOfEmployee($employeeId, $date);
    public function approveOtRequest($id);
    public function declineOtRequest($id);

}
