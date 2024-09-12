<?php

namespace App\Repositories\Contracts;

interface TopicRepositoryInterface
{
    public function createTopic($name);
    public function findByName($name);
    public function getAllTopics();
    public function findTopicById($topicId);
}
