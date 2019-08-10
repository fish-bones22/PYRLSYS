<?php

namespace App\Http\Controllers;

use App\Contracts\IUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Auth;

class AuthUtility extends Controller {

    public static function checkAuth($pageKey) {

        if (Auth::user() == null)
            return true;

        $auth = Auth::user()->session_accesses;
        $isAdmin = Auth::user()->admin;
        if (!Auth::check() || !(in_array($pageKey, $auth) || $isAdmin))
            return true;

        return false;
    }

    public static function hasAuth($pageKey) {

        if (Auth::user() == null)
            return false;

        $auth = Auth::user()->session_accesses;
        $isAdmin = Auth::user()->admin;

        if (!Auth::check() || !(in_array($pageKey, $auth) || $isAdmin))
            return false;

        return true;
    }

    public static function hasDepartmentAccess($departmentId) {
        if (Auth::user() == null)
            return false;

        $auth = Auth::user()->session_departments;

        if (!in_array($departmentId, $auth)) {
            return false;
        }

        return true;
    }

    public static function isLogged() {
        if (!Auth::check())
            return false;
        return true;
    }

    public static function redirect() {
        return redirect()->route('login');
    }
}
