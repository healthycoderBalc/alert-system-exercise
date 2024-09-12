<?php

namespace App\Repositories\Implementations;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Models\User;
use App\Repositories\Contracts\TopicRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    protected static $users = [];
    protected $topicRepository;

    public function __construct(TopicRepositoryInterface $topicRepository)
    {
        $this->topicRepository = $topicRepository;
    }

    public function createUser($name, $email)
    {
        $user = new User($name, $email);
        self::$users[] = $user;

        return $user;
    }

    public function emailExist($email)
    {
        foreach (self::$users as $user) {
            if ($user->getEmail() == $email) {
                return true;
            }
        }
        return false;
    }

    public function findUserById($userId)
    {
        foreach (self::$users as $user) {
            if ($user->getId() == $userId) {
                return $user;
            }
        }
        return null;
    }
}
