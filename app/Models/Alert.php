<?php

namespace App\Models;

use DateTime;

class Alert
{
    private $id;
    private $description;
    private $topicId;
    private $alertTypeId;
    private $createdAt;
    private $expirationDate;
    const ALERT_TYPE_URGENT = 1;
    const ALERT_TYPE_INFORMATIVE = 2;

    public function __construct($description, $topicId, $alertTypeId, DateTime $createdAt, $expirationDate = null, $id = null)
    {
        $this->description = $description;
        $this->topicId = $topicId;
        $this->alertTypeId = $alertTypeId;
        $this->createdAt = $createdAt;
        $this->expirationDate = $expirationDate;
        $this->id = $id ?? uniqid();
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getTopicId()
    {
        return $this->topicId;
    }

    public function getAlertTypeId()
    {
        return $this->alertTypeId;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    public function isExpired()
    {
        if ($this->expirationDate === null) {
            return false;
        }
        $now = new DateTime();
        return $now > $this->expirationDate;
    }

    public function isUrgent()
    {
        return $this->alertTypeId == self::ALERT_TYPE_URGENT;
    }

    public function isInformative()
    {
        return $this->alertTypeId == self::ALERT_TYPE_INFORMATIVE;
    }
}
