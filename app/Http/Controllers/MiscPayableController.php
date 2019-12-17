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
    public function view13thMonthPay() {

        $departments = $this->categoryService->getCategories('department');
        $employees = $this->employeeService->getAllEmployees();
        return response()->view('payroll.13thmonth', ['departments' => $departments, 'employees' => $employees]);
    }

    /**
     * POST Save 13th month pay records
     */
    public function set13thMonthPay(Request $request) {

        $departments = $this->categoryService->getCategories('department');
        $employees = $this->employeeService->getAllEmployees();
        return response()->view('payroll.13thmonth', ['departments' => $departments, 'employees' => $employees]);
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
        }

        return response()->json([
            'total' => round($total, 2),
            'breakdown' => $basicPays
        ]);

    }


}
