<?php

namespace App\Repositories\Contracts;

interface UserAlertRepositoryInterface
{
    public function getAllUserAlerts();
    public function getById($userAlertId);
    public function createUserAlert($alertId, $userId);
    public function getUnreadUserAlertsByUser($userId);
    public function getUserAlertsByAlertId($alertId);
}
