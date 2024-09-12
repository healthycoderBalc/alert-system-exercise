<?php

namespace App\Models;


class UserAlert
{
    private $id;
    private $alertId;
    private $userId;
    private $read;
    private $readAt;

    public function __construct($alertId, $userId, $id = null, $read = false, $readAt = null)
    {
        $this->alertId = $alertId;
        $this->userId = $userId;
        $this->id = $id ?? uniqid();
        $this->read = $read;
        $this->readAt = $readAt;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getAlertId()
    {
        return $this->alertId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function isRead()
    {
        return $this->read;
    }

    public function getReadAt()
    {
        return $this->readAt;
    }

    public function markAsRead()
    {
        $this->read = true;
        $this->readAt = new \DateTime();
    }
}
