<?php

namespace App\Http\Controllers;

use App\Entities\MiscPayableEntity;
use App\Contracts\ICategoryService;
use App\Contracts\IMiscPayableService;
use App\Contracts\IPayrollService;
use DateInterval;
use DatePeriod;
use Illuminate\Http\Request;
use App\Contracts\IEmployeeService;

class MiscPayableController extends Controller
{
    private $payableService;
    private $payrollService;
    private $categoryService;
    private $employeeService;
    private $key = "13thmonthpay";

    public function __construct(IMiscPayableService $payableService, IPayrollService $payrollService, ICategoryService $categoryService, IEmployeeService $employeeService)
    {
        $this->payableService = $payableService;
        $this->payrollService = $payrollService;
        $this->categoryService = $categoryService;
        $this->employeeService = $employeeService;
    }

    /**
     * GET 13th month pay page
     */
    public function view13thMonthPay(Request $request) {

        $details = array();
        if ($request->from !== null) {
            $date = date_create($request->from);
            $details['monthfrom'] = $date->format('m');
            $details['yearfrom'] = $date->format('Y');
            $details['startdate'] = $date->format('Y-m-d');
            $records = $this->payableService->getRecord($date, $this->key);
            if (!isset($records['result'])) {
                $details['records'] = $records;
            }
        }
        if ($request->to !== null) {
            $date = date_create($request->to);
            $details['monthto'] = $date->format('m');
            $details['yearto'] = $date->format('Y');
            $details['enddate'] = $date->format('Y-m-d');
        }
        $departments = $this->categoryService->getCategories('department');
        $employees = $this->employeeService->getAllEmployees();
        return response()->view('payroll.13thmonth', ['details' => $details, 'departments' => $departments, 'employees' => $employees]);
    }

    /**
     * POST Save 13th month pay records
     */
    public function set13thMonthPay(Request $request) {

        if ($request->startdate === null) {
            return redirect()->back()->with('error', 'No start date');
        }

        if ($request->included !== null) {
            foreach ($request->included as $key => $val) {
                if ($val !== 'on') continue;
                if ($request->amount[$key] === 0) continue;

                // Map form fields to entity
                $entity = new MiscPayableEntity();

                var_dump($key);
                $entity->employee_id = (int)$key;
                $entity->employeeName = $request->name[$key];
                $entity->amount = $request->amount[$key];
                $entity->department = $request->department[$key];
                $entity->key = $this->key;
                $entity->displayName = '13th Month Pay';
                $entity->recordDate = $request->startdate;
                // Save or update record
                $result = $this->payableService->add($entity);

                if (!$result['result']) {
                    return redirect()->back()->with('error', $result['message']);
                }
            }
        }
        return redirect()->back()->with('success', 'Successfully saved records');
    }

    public function set13thMonthPayDate(Request $request) {
        $monthFrom = $request->monthfrom !== null ? $request->monthfrom : '01';
        $monthTo = $request->monthto !== null ? $request->monthto : '12';
        $yearFrom = $request->yearfrom !== null ? $request->yearfrom : now()->format('Y');
        $yearTo = $request->yearto !== null ? $request->yearto : now()->format('Y');
        return redirect()->action('MiscPayableController@view13thMonthPay', ['from' => $yearFrom.'-'.$monthFrom.'-01', 'to' => $yearTo.'-'.$monthTo.'-01']);
    }

    /**
     * AJAX POST get payroll
     */
    public function ajax_getPayrollFromMonthRange(Request $request) {

        if ($request->from === null) {
            return response()->json('ERROR:No starting date');
        }

        if ($request->to === null) {
            return response()->json('ERROR:No end date');
        }

        if ($request->id === null) {
            return response()->json('ERROR:No employee ID');
        }

        $dateFrom = $request->from;
        $dateTo = $request->to;
        $employeeId = $request->id;

        // Initialize dates
        $objDateFrom = date_create($dateFrom);
        $objDateTo = date_create($dateTo);
        $interval = DateInterval::createFromDateString('1 month');
        $dateRange = new DatePeriod($objDateFrom, $interval, $objDateTo);

        $total = 0;
        $basicPays = array();
        $ind = 0;
        foreach ($dateRange as $date) {

            // Get second period pay
            $payroll = $this->payrollService->getBasicPay($employeeId, $date);
            $basicPay = $payroll->basicPay;
            $total += $basicPay;
            $basicPays[] = [
                'date' => $basicPay,
                'amount' => $date->format('Y-m-d')
            ];

            // Get first period pay
            $payroll = $this->payrollService->getBasicPay($employeeId, $date->modify('15 days'));
            $basicPay = $payroll->basicPay;
            $total += $basicPay;
            $basicPays[] = [
                'date' => $basicPay,
                'amount' => $date->format('Y-m-d')
            ];
            $ind++;
        }

        return response()->json([
            'total' => round($total / ($ind === 0 ? 1 : $ind), 2),
            'breakdown' => $basicPays
        ]);

    }


}
