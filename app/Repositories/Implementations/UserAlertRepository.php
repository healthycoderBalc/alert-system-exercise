<?php

namespace App\Repositories\Implementations;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\UserAlertRepositoryInterface;
use App\Models\UserAlert;

class UserAlertRepository implements UserAlertRepositoryInterface
{
    protected static $userAlerts = [];
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUserAlerts()
    {
        return self::$userAlerts;
    }



    public function getById($userAlertId)
    {
        foreach (self::$userAlerts as $userAlert) {
            if ($userAlert->getId() == $userAlertId) {
                return $userAlert;
            }
        }
        return null;
    }

    public function createUserAlert($alertId, $userId)
    {
        $userAlert = new UserAlert($alertId, $userId);
        self::$userAlerts[] = $userAlert;

        return $userAlert;
    }

    public function getUnreadUserAlertsByUser($userId)
    {
        return array_filter(self::$userAlerts, function ($userAlert) use ($userId) {
            return !$userAlert->isRead() && $userAlert->getUserId() === $userId;
        });
    }

    public function getUserAlertsByAlertId($alertId)
    {
        $userAlerts =  array_filter(self::$userAlerts, function ($userAlert) use ($alertId) {
            return $userAlert->getAlertId() == $alertId;
        });
        return $userAlerts ?? [];
    }
}
