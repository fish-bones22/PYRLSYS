<?php

namespace App\Services;

use App\Contracts\IUserService;
use App\Entities\UserEntity;
use App\Models\DepartmentAccess;
use App\Models\User;
use App\Models\UserAccess;
use App\Models\UserRole;
use Auth;
use Illuminate\Support\Facades\Hash;


class UserService extends EntityService implements IUserService {

    public function getAllUsers() {

        $users = User::all();
        $userEntities = array();
        foreach ($users as $user) {
            if ($user->username === 'dev')
                continue;
            $userEntities[] = $this->mapToEntity($user, new UserEntity());
        }
        return $userEntities;
    }

    protected function mapToEntity($model, $entity) {
        $entity = parent::mapToEntity($model, $entity);
        $entity->fullName = $model->fullName;
        $entity->username = $model->username;
        $entity->password = $model->password;
        $entity->admin = $model->admin;

        if ($model->accesses != null && sizeof($model->accesses) != 0) {
            $entity->accesses = array();

            foreach ($model->accesses as $access) {
                $entity->accesses[] = $access->user_role_id;
            }
        }

        if ($model->departmentAccesses != null && sizeof($model->departmentAccesses) != 0) {
            $entity->departmentAccesses = array();

            foreach ($model->departmentAccesses as $departmentAccess) {
                $entity->departmentAccesses[] = $departmentAccess->category_id;
            }
        }

        return $entity;
    }

    public function getUserById($id) {

        $user = User::find($id);
        $entity = $this->mapToEntity($user, new UserEntity());
        return $entity;

    }


    public function getUserByUsername($username) {

        $user = User::where('username', $username)->first();
        $entity = $this->mapToEntity($user);
        return $entity;

    }


    public function login($username, $password) {

        if (!Auth::attempt(['username' => $username, 'password' => $password])) {
            return false;
        }

        $id = User::where('username', $username)->first()->id;

        return $id;

    }


    public function logout() {
        Auth::logout();
    }


    public function userExists($username, $password) {
        $user = User::where('username', $username)->first();

        if ($user == null)
            return false;

        return Hash::check($password, $user->password);
    }


    public function checkIfAdmin($username, $password) {
        $hashed = Hash::make($password);
        $user = User::where('username', $username)->where('admin', 1)->first();

        if ($user == null)
            return false;

        return Hash::check($password, $user->password);
    }


    public function registerUser(UserEntity $user) {

        $model = new User();

        $model->username = $user->username;
        $model->fullName = $user->fullName;
        $model->password = Hash::make($user->password);
        $model->admin = $user->admin;
        $model->save();

        $id = User::orderBy('created_at', 'desc')->first()->id;

        if (sizeof($user->accesses) != 0) {
            foreach($user->accesses as $access) {
                $acc = new UserAccess();
                $acc->user_id = $id;
                $acc->user_role_id = $access;
                $acc->save();
            }
        }

        if (sizeof($user->departmentAccesses) != 0) {
            foreach($user->departmentAccesses as $departmentAccess) {
                $acc = new DepartmentAccess();
                $acc->user_id = $id;
                $acc->category_id = $departmentAccess;
                $acc->save();
            }
        }

    }


    public function deleteUser($id) {
        User::find($id)->delete();
    }


    public function updateUser(UserEntity $user) {

        $id = $user->id;
        $model = User::find($id);

        $model->username = $user->username;
        $model->fullName = $user->fullName;
        $model->password = $user->password != null ? Hash::make($user->password) : $model->password;
        $model->admin = $user->admin;
        $model->save();

        $userAccesses = $user->accesses;
        UserAccess::where('user_id', $id)->delete();
        if (sizeof($userAccesses) != 0) {

            foreach ($userAccesses as $access) {
                $userAccess = new UserAccess();
                $userAccess->user_id = $id;
                $userAccess->user_role_id = $access;
                $userAccess->save();
            }

        }

        $departmentAccess = $user->departmentAccesses;
        DepartmentAccess::where('user_id', $id)->delete();
        if (sizeof($departmentAccess) != 0) {

            foreach ($departmentAccess as $access) {
                $userAccess = new DepartmentAccess();
                $userAccess->user_id = $id;
                $userAccess->category_id = $access;
                $userAccess->save();
            }

        }

    }


    public function getUserAccess($id) {

        $user = User::find($id);
        $userAccesses = $user->accesses;
        $accesses = array();

        foreach ($userAccesses as $access) {
            $accesses[] = [
                'id' => $access->user_role_id
            ];
        }

        return $accesses;

    }

    public function hasAccess($id, $pageKey) {

        if ($this->easyCheckIfAdmin($id))
            return true;

        $userRole = UserRole::where('roleKey', $pageKey)->first();

        if ($userRole == null)
            return false;

        $access = UserAccess::where('user_id', $id)->where('user_role_id', $userRole->id)->first();

        if ($access == null)
            return false;

        return true;
    }

    public function getDepartmentAccess($id) {


        $departmentAccesses = DepartmentAccess::where('employee_id', $id);
        if ($departmentAccess == null)
            return array();

        $accesses = array();

        foreach ($departmentAccesses as $access) {
            $accesses[$access->category_id] = $access->category_id;
        }

        return $accesses;
    }

    private function easyCheckIfAdmin($id) {
        $user = User::where('id', $id)->where('admin', true)->first();

        if ($user == null)
            return false;

        return true;
    }

}
