<?php

namespace App\Repositories\Implementations;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\TopicRepositoryInterface;
use App\Repositories\Contracts\UserTopicRepositoryInterface;
use App\Models\UserTopic;

class UserTopicRepository implements UserTopicRepositoryInterface
{
    protected static $userTopics = [];
    protected $userRepository;
    protected $topicRepository;

    public function __construct(UserRepositoryInterface $userRepository, TopicRepositoryInterface $topicRepository)
    {
        $this->userRepository = $userRepository;
        $this->topicRepository = $topicRepository;
    }

    public function getAllUserTopics()
    {
        return self::$userTopics;
    }

    public function getById($userTopicId)
    {
        foreach (self::$userTopics as $userTopic) {
            if ($userTopic->getId() == $userTopicId) {
                return $userTopic;
            }
        }
        return null;
    }

    public function subscribeUserToTopic($topicId, $userId)
    {
        if (!$this->topicRepository->findTopicById($topicId)) {
            throw new \Exception("El tema ID: {$topicId} no existe.");
        }

        if (!$this->userRepository->findUserById($userId)) {
            throw new \Exception("El usuario ID: {$userId} no existe.");
        }


        if ($this->userTopicExist($topicId, $userId)) {
            throw new \Exception(`El tema ID: {$topicId} ya estaba asociado al usuario ID: {$userId}.`);
        }

        $userTopic = new UserTopic($topicId, $userId);
        self::$userTopics[] = $userTopic;

        return $userTopic;
    }

    public function userTopicExist($topicId, $userId)
    {
        foreach (self::$userTopics as $userTopic) {
            if ($userTopic->getTopicId() == $topicId && $userTopic->getUserId() == $userId) {
                return true;
            }
        }
        return false;
    }
    public function getUsersByTopicId($topicId)
    {
        $usersListKeys = [];
        foreach (self::$userTopics as $userTopic) {
            if ($userTopic->getTopicId() == $topicId) {
                $usersListKeys[$userTopic->getUserId()] = true;
            }
        }
        $usersListIds = array_keys($usersListKeys);
        $usersList = [];

        foreach ($usersListIds as $userId) {
            $user = $this->userRepository->findUserById($userId);
            if ($user) {
                $usersList[] = $user;
            }
        }

        return $usersList;
    }

    public function getTopicsByUserId($userId)
    {
        $topicListKeys = [];
        foreach (self::$userTopics as $userTopic) {
            if ($userTopic->getUserId() == $userId) {
                $topicListKeys[$userTopic->getTopicId()] = true;
            }
        }
        $topicListIds = array_keys($topicListKeys);
        $topicList = [];

        foreach ($topicListIds as $topicId) {
            $topic = $this->topicRepository->findTopicById($topicId);
            if ($topic) {
                $topicList[] = $topic;
            }
        }

        return $topicList;
    }
}
