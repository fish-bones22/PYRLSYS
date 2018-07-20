<?php

namespace App\Services;

use App\Contracts\IUserService;
use App\Entities\UserEntity;
use App\Models\User;
use App\Models\UserAccess;
use Auth;
use Illuminate\Support\Facades\Hash;


class UserService implements IUserService {

    public function getAllUsers() {

        $users = User::all();
        $userEntities = array();
        foreach ($users as $user) {
            $userEntities[] = $this->mapToEntity($user);
        }
        return $userEntities;
    }

    private function mapToEntity($model) {

        $entity = new UserEntity();
        $entity->id = $model->id;
        $entity->fullName = $model->fullName;
        $entity->username = $model->username;
        $entity->password = $model->password;
        $entity->admin = $model->admin;

        if (sizeof($model->accesses) != 0) {
            $entity->accesses = array();

            foreach ($model->accesses as $access) {
                $entity->accesses[] = $access->user_role_id;
            }
        }

        return $entity;
    }

    public function getUserById($id) {

        $user = User::find($id);
        $entity = $this->mapToEntity($user);
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

}
