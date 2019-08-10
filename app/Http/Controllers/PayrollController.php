<?php

namespace App\Http\Controllers;

use App\Contracts\ICategoryService;
use App\Contracts\IEmployeeService;
use App\Contracts\IManhourService;
use App\Contracts\IPayrollService;
use App\Entities\PayrollEntities;

use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public $payrollService;
    public $employeeService;
    public $manhourService;
    public $categoryService;
    private $pageKey = 'payrollmanagement';

    public function __construct (IPayrollService $payrollService, IEmployeeService $employeeService, IManhourService $manhourService, ICategoryService $categoryService) {
        $this->payrollService = $payrollService;
        $this->employeeService = $employeeService;
        $this->manhourService = $manhourService;
        $this->categoryService = $categoryService;
    }


    public function index() {
        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();
        $employees = $this->employeeService->getAllEmployees('lastname');
        $departments = $this->categoryService->getCategories('department');
        return view('payroll.index', ['employees' => $employees, 'departments' => $departments ]);
    }

    public function viewNow($id) {
        $date = date_format(now(), 'Y-m-d');
        return redirect()->action('PayrollController@viewPay', ['id' => $id, 'date' => $date]);
    }


    public function setRecordDate(Request $request, $id) {

        $month = $request->get('month');
        $year = $request->get('year');
        $period = $request->get('period');
        $day = $period === 'first' ? '16' : '01';

        return redirect()->action('PayrollController@viewPay', ['id' => $id, 'date' => $year.'-'.$month.'-'.$day]);

    }

    public function viewPay($id, $date) {

        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();

        $day = date_format(date_create($date),'d');
        $year = date_format(date_create($date), 'Y');
        $month = date_format(date_create($date), 'm');

        $startDay = $day <= 15 ? '01' : '16';
        $date = $year.'-'.$month.'-'.$startDay;

        $details = [
            'date' => $year.'-'.$month.'-'.$startDay,
            'startday' => $startDay,
            'month' => $month,
            'year' => $year
        ];

        $employee = $this->employeeService->getEmployeeById($id);

        if ($employee == null)
            return redirect()->action('PayrollController@index');
        $payroll = $this->payrollService->getPayroll($id, date_create($date));
        return view('payroll.viewpay', ['employee' => $employee, 'details' => $details, 'payroll' => $payroll ]);

    }

    public function deductibles($id, $date) {
        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();
        return view('payroll.deductibles');
    }

    public function goToDateSummary(Request $request) {

        $month = $request->get('month');
        $year = $request->get('year');
        $period = $request->get('period');
        $day = $period === 'first' ? '16' : '01';

        return redirect()->action('PayrollController@summary', ['date' => $year.'-'.$month.'-'.$day]);
    }

    public function summary($date) {

        $day = date_format(date_create($date),'d');
        $year = date_format(date_create($date), 'Y');
        $month = date_format(date_create($date), 'm');

        $startDay = $day <= 15 ? '01' : '16';
        $date = $year.'-'.$month.'-'.$startDay;

        $details = [
            'date' => $year.'-'.$month.'-'.$startDay,
            'startday' => $startDay,
            'month' => $month,
            'year' => $year
        ];

        $employees = $this->employeeService->getAllEmployees('lastname');
        $summary = array();
        foreach ($employees as $emp) {
            $payroll = $this->payrollService->getPayroll($emp->id, date_create($date));
            $summary[$emp->id] = $payroll;
        }

        $departments = $this->categoryService->getCategories('department');
        return view('payroll.summary', ['departments' => $departments, 'employees' => $employees, 'details' => $details, 'summary' => $summary]);
    }

    public function getPay($id, $date) {

        $day = date_format(date_create($date),'d');
        $year = date_format(date_create($date), 'Y');
        $month = date_format(date_create($date), 'm');

        $startDay = $day <= 15 ? '01' : '16';
        $date = $year.'-'.$month.'-'.$startDay;


        $payroll = $this->payrollService->getPayroll($id, date_create($date));
        return json_encode($payroll);

    }

    public function getEmployees($date) {
        $day = date_format(date_create($date),'d');
        $year = date_format(date_create($date), 'Y');
        $month = date_format(date_create($date), 'm');

        $startDay = $day <= 15 ? '01' : '16';
        $date = $year.'-'.$month.'-'.$startDay;


        $employees = $this->employeeService->getAllEmployees('lastname');
        return json_encode($employees);

    }
}
