<?php

namespace App\Repositories\Contracts;

use App\Models\Alert;

interface AlertRepositoryInterface
{
    public function createAlert(Alert $alert);
    public function getAlertById($alertId);

    public function getUnreadUnexpiredAlertsByUser($userId);

    public function getAlertsByTopic($topicId);
}
