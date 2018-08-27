<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\IUserService;

class HomeController extends Controller
{

    private $userService;

    public function __construct(IUserService $userService) {
        $this->userService = $userService;
    }

    public function index() {

        if (!AuthUtility::isLogged())
            return redirect()->action('UserController@login');

        $auth = array();

        $auth = [
            'admin' => AuthUtility::hasAuth('accountsmanagement'),
            'human' => AuthUtility::hasAuth('humanresourcemanagement'),
            'manhour' => AuthUtility::hasAuth('manhourmanagement'),
            'payroll' => AuthUtility::hasAuth('payrollmanagement')
        ];

        return view('welcome', ['auth' => $auth]);
    }
}
