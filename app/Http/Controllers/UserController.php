<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

use App\Contracts\IUserRoleService;
use App\Contracts\IUserService;
use App\Contracts\ICategoryService;
use App\Entities\UserEntity;
use App\Http\Controllers\Controller;
use Auth;


class UserController extends Controller {

    private $userService;
    private $userRoleService;
    private $categoryService;
    private $pageKey = 'accountsmanagement';


    public function __construct(IUserService $userService, IUserRoleService $userRoleService, ICategoryService $categoryService) {
        $this->userService = $userService;
        $this->userRoleService = $userRoleService;
        $this->categoryService = $categoryService;
    }


    public function index() {

        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();

        $users = $this->userService->getAllUsers();
        return view('user.index', compact('users'));
    }


    public function login(Request $request) {

        if ($request->isMethod('GET'))
            return view('user.login');

        $req = $request->all();

        $result = $this->userService->login($req['username'], $req['password']);

        if (!$result) {
            return redirect()->back()->with('error', 'Incorrect username or password');
        }

        session([
            'username' => $req['username'],
            'id' => $result
        ]);

        return redirect('/');
    }

    public function logout() {

        $this->userService->logout();
        Session::flush();
        return redirect()->action('UserController@login');

    }


    public function register() {

        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();

        $roles = $this->userRoleService->getAllRoles();
        $categories = $this->categoryService->getCategoriesNofilter('department');
        return view('user.register', ['roles' => $roles, 'categories' => $categories]);

    }


    public function addUser(Request $request) {

        $req = $request->all();

        if ($req['password'] != $req['confirm_password'])
            return redirect()->back()->withInputs($req)->with('error', 'Passwords do not match');

        if ($this->userService->usernameExists($req['username'])) {
            $request->flush();
            return redirect()->back()->withInputs($request->all())->with('error', 'Username already in use');
        }

        $user = new UserEntity();
        $user->fullName = $req['full_name'];
        $user->username = $req['username'];
        $user->password = $req['password'];
        $user->admin = isset($req['admin']);

        if (isset($req['user_accesses']) && sizeof($req['user_accesses']) != 0) {

            $user->accesses = array();
            $userAccess = $req['user_accesses'];
            for($i = 0; $i < sizeof($userAccess); $i++) {

                if (!isset($userAccess[$i]['key']))
                    continue;

                $user->accesses[] = $userAccess[$i]['id'];
            }
            // Departments
            $user->departmentAccesses = array();
            $departments = $req['department_accesses'];
            for($i = 0; $i < sizeof($departments); $i++) {

                if (!isset($departments[$i]['key']))
                    continue;

                $user->departmentAccesses[] = $departments[$i]['id'];
            }

        }

        $this->userService->registerUser($user);

        return redirect()->action('UserController@index');
    }


    public function getUser($id) {

        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();

        $user = $this->userService->getUserById($id);
        $roles = $this->userRoleService->getAllRoles();
        $categories = $this->categoryService->getCategoriesNofilter('department');
        return view('user.update', ['user' => $user, 'roles' => $roles, 'categories' => $categories]);
    }


    public function updateUser(Request $request, $id) {

        $req = $request->all();

        $user = $this->userService->getUserById($id);
        $currentUsername = $user->username; // Store current username for verification purposes
        $user->fullName = $req['full_name'];
        $user->username = $req['username'];
        $user->admin = isset($req['admin']);
        $user->password = null;

        // Change password if Forgot Password is checked or an Old Password is supplied
        if (isset($req['forgot_password']) || $req['password'] != '') {

            $adminVerify = false;
            $userExists = false;
            // If Forgot Password is checked, verify supplied admin credentials
            if (isset($req['forgot_password'])) {
                $adminVerify = $this->userService->checkIfAdmin($req['admin_username'], $req['admin_password']);
            }
            // Else assume that an old password is supplied
            else {
                $userExists = $this->userService->userExists($currentUsername, $req['password']);
            }
            // Change password here
            if ($adminVerify || $userExists) {
                // Make sure confirmation password is correct
                if ($req['new_password'] != $req['confirm_new_password']) {
                    return redirect()->action('UserController@getUser', $id)->with('error', 'Passwords do not match');
                }
                // Assign password
                $user->password = $req['new_password'];
            }
            else {
                if (isset($req['forgot_password']))
                    return redirect()->action('UserController@getUser', $id)->with('error', 'Incorrect admin username or password');
                return redirect()->action('UserController@getUser', $id)->with('error', 'Incorrect password');
            }

        }

        if (sizeof($req['user_accesses']) != 0) {

            $user->accesses = array();
            $userAccess = $req['user_accesses'];

            for($i = 0; $i < sizeof($userAccess); $i++) {

                if (!isset($userAccess[$i]['key']))
                    continue;

                $user->accesses[] = $userAccess[$i]['id'];
            }
        }

        if (sizeof($req['department_accesses']) != 0) {

            $user->departmentAccesses = array();
            $departmentAccesses = $req['department_accesses'];

            for($i = 0; $i < sizeof($departmentAccesses); $i++) {

                if (!isset($departmentAccesses[$i]['key']))
                    continue;

                $user->departmentAccesses[] = $departmentAccesses[$i]['id'];
            }
        }

        $this->userService->updateUser($user);

        return redirect()->action('UserController@index');

    }


    public function deleteUser($id) {

        $this->userService->deleteUser($id);

        return redirect()->action('UserController@index');

    }
}
