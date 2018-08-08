<?php

namespace App\Http\Controllers;

use App\Contracts\ICategoryService;
use App\Contracts\IEmployeeService;
use Illuminate\Http\Request;

class OtRequestController extends Controller
{
    private $categoryService;
    private $employeeService;

    public function __construct(ICategoryService $categoryService, IEmployeeService $employeeService) {

        $this->categoryService = $categoryService;
        $this->employeeService = $employeeService;
    }

    public function index() {

    }

    public function add() {
        $departments = $this->categoryService->getCategories('department');
        return view('otrequest.new', [ 'departments' => $departments ]);
    }

    public function getEmployees($dept) {

        $employees = $this->employeeService->getEmployeesByDepartment($dept);

        if ($employees == null) return null;

        $empJson = array();
        foreach ($employees as $emp) {
            $empJson[] = [
                'id' => $emp->id,
                'name' => $emp->fullName,
                'employeeId' => $emp->employeeId,
                'timecard' => $emp->details['timecard']['value']
            ];
        }

        return response()->json($empJson);
    }
}
