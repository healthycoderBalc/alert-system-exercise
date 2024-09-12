<?php

namespace App\Repositories\Implementations;

use App\Models\Topic;
use App\Repositories\Contracts\TopicRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

class TopicRepository implements TopicRepositoryInterface
{
    protected static $topics = [];
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    public function createTopic($name)
    {
        $topic = new Topic($name);
        self::$topics[] = $topic;
        return $topic;
    }

    public function findByName($name)
    {

        foreach (self::$topics as $topic) {
            if (strcasecmp($topic->getName(), $name) === 0) {
                return $topic;
            }
        }
        return null;
    }

    public function getAllTopics()
    {
        return self::$topics;
    }

    public function findTopicById($topicId)
    {
        foreach (self::$topics as $topic) {
            if ($topic->getId() == $topicId) {
                return $topic;
            }
        }
        return null;
    }
}
