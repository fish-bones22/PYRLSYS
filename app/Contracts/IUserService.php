<?php

namespace App\Contracts;

use App\Entities\UserEntity;

interface IUserService {

    public function getAllUsers();
    public function getUserById($id);
    public function getUserByUsername($username);
    public function login($username, $password);
    public function logout();
    public function userExists($username, $password);
    public function checkIfAdmin($username, $password);
    public function registerUser(UserEntity $user);
    public function deleteUser($id);
    public function updateUser(UserEntity $user);
    public function getUserAccess($id);

}
