<?php

namespace App\Models;

class UserTopic
{
    private $id;
    private $topicId;
    private $userId;

    public function __construct(
        $topicId,
        $userId,
        $id = null
    ) {
        $this->topicId = $topicId;
        $this->userId = $userId;
        $this->id = $id ?? uniqid();
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getTopicId()
    {
        return $this->topicId;
    }

    public function getUserId()
    {
        return $this->userId;
    }
}
