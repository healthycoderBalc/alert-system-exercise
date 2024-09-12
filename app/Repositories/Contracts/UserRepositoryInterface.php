<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function createUser($name, $email);
    public function emailExist($email);
    public function findUserById($userId);
}
