<?php

namespace App\Repositories\Implementations;

use App\Repositories\Contracts\AlertRepositoryInterface;
use App\Repositories\Contracts\UserAlertRepositoryInterface;
use App\Models\Alert;

class AlertRepository implements AlertRepositoryInterface
{
    protected static $alerts = [];
    private $userAlertRepository;


    public function __construct(UserAlertRepositoryInterface $userAlertRepository)
    {
        $this->userAlertRepository = $userAlertRepository;
    }

    public function createAlert(Alert $alert)
    {
        self::$alerts[] = $alert;
    }

    public function getAlertById($alertId)
    {
        foreach (self::$alerts as $alert) {
            if ($alert->getId() == $alertId) {
                return $alert;
            }
        }
        return null;
    }

    public function getUnreadUnexpiredAlertsByUser($userId)
    {
        $unreadUnexpiredAlertsByUser = [];
        $unreadUserAlertsByUser = $this->userAlertRepository->getUnreadUserAlertsByUser($userId);
        foreach ($unreadUserAlertsByUser as $userAlert) {
            $alert = $this->getAlertById($userAlert->getAlertId());
            if (!$alert->isExpired()) {
                $unreadUnexpiredAlertsByUser[] = $alert;
            }
        }
        return $unreadUnexpiredAlertsByUser;
    }

    public function getAlertsByTopic($topicId)
    {
        $filteredAlerts = array_filter(self::$alerts, function ($alert) use ($topicId) {
            return $alert->getTopicId() === $topicId && !$alert->isExpired();
        });

        return array_values($filteredAlerts) ?: [];
    }
}
