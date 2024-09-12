<?php

namespace App\Repositories\Contracts;

interface UserTopicRepositoryInterface
{
    public function getAllUserTopics();
    public function getById($userTopicId);
    public function subscribeUserToTopic($topicId, $userId);
    public function userTopicExist($topicId, $userId);
    public function getUsersByTopicId($topicId);
    public function getTopicsByUserId($userId);
}
