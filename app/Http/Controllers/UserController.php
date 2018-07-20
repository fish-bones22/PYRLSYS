<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

use App\Contracts\IUserRoleService;
use App\Contracts\IUserService;
use App\Entities\UserEntity;
use App\Http\Controllers\Controller;
use Auth;


class UserController extends Controller {

    private $userService;
    private $userRoleService;

    public function __construct(IUserService $userService, IUserRoleService $userRoleService) {
        $this->userService = $userService;
        $this->userRoleService = $userRoleService;
    }


    public function index() {

        if (!Auth::check())
            return redirect()->action('UserController@login');

        $users = $this->userService->getAllUsers();
        return view('user.index', compact('users'));
    }


    public function login(Request $request) {

        if ($request->isMethod('GET'))
            return view('user.login');

        $req = $request->all();

        $result = $this->userService->login($req['username'], $req['password']);

        if (!$result) {
            return view('user.login')->with('error', 'Incorrect username or password');
        }

        session([
            'username' => $req['username'],
            'id' => $result
        ]);

        return redirect('/');
    }

    public function logout() {

        Session::flush();
        $this->userService->logout();
        return redirect()->action('UserController@login');

    }


    public function register() {

        $roles = $this->userRoleService->getAllRoles();
        return view('user.register', compact('roles'));

    }


    public function addUser(Request $request) {

        $req = $request->all();

        if ($req['password'] != $req['confirm_password'])
            return view('user.register').withErrors('Passwords do not match');

        $user = new UserEntity();
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

        }

        $this->userService->registerUser($user);

        return redirect()->action('UserController@index');
    }


    public function getUser($id) {
        $user = $this->userService->getUserById($id);
        $roles = $this->userRoleService->getAllRoles();
        return view('user.update', compact('user'), compact('roles'));
    }


    public function updateUser(Request $request, $id) {

        $req = $request->all();

        $user = $this->userService->getUserById($id);
        $currentUsername = $user->username; // Store current username for verification purposes
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

        $this->userService->updateUser($user);

        return redirect()->action('UserController@index');

    }


    public function deleteUser($id) {

        $this->userService->deleteUser($id);

        return redirect()->action('UserController@index');

    }
}
