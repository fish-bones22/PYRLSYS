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

    public function viewPay($id, $date) {

        $employee = $this->employeeService->getEmployeeById($id);

        if ($employee == null)
            return redirect()->action('PayrollController@index');
        $payroll = $this->payrollService->getPayroll($id, date_create($date));
        return view('payroll.viewpay', ['employee' => $employee, 'details' => array(), 'payroll' => $payroll ]);

    }

    public function deductibles($id, $date) {
        return view('payroll.deductibles');
    }
}
