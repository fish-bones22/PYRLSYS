<?php

namespace App\Contracts;

use App\Entities\ManhourEntity;

interface IManhourService {

    public function getAllRecords();
    public function recordManhour(ManhourEntity $entity);
    public function getRecord($id, $date);
    public function getAllRecordsByDateRange($datefrom, $dateto);
    public function getSummaryOfRecord($employeeId, $date);
    public function getSummaryOfRecordsByDateRange($datefrom, $dateto);
    public function getHoliday($date);
    public function getHolidays($dateFrom, $dateTo);
    public function saveHoliday($holiday);
    public function deleteHoliday($date);

}
