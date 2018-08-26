<?php

namespace App\Http\Controllers;

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

    public function __construct (IPayrollService $payrollService, IEmployeeService $employeeService, IManhourService $manhourService) {
        $this->payrollService = $payrollService;
        $this->employeeService = $employeeService;
        $this->manhourService = $manhourService;
    }


    public function index() {
        $employees = $this->employeeService->getAllEmployees('lastname');
        return view('payroll.index', ['employees' => $employees ]);
    }

    public function viewNow($id) {
        $date = date_format(now(), 'Y-m-d');
        return redirect()->action('PayrollController@viewPay', ['id' => $id, 'date' => $date]);
    }


    public function setRecordDate(Request $request, $id) {

        $month = $request->get('month');
        $year = $request->get('year');
        $period = $request->get('period');
        $day = $period === 'first' ? '17' : '01';

        return redirect()->action('PayrollController@viewPay', ['id' => $id, 'date' => $year.'-'.$month.'-'.$day]);

    }

    public function viewPay($id, $date) {

        $day = date_format(date_create($date),'d');
        $year = date_format(date_create($date), 'Y');
        $month = date_format(date_create($date), 'm');

        $startDay = $day <= 16 ? '01' : '17';
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
        return view('payroll.deductibles');
    }
}
