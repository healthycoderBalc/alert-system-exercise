<?php

namespace App\Services;

use Exception;


use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\AlertRepositoryInterface;
use App\Repositories\Contracts\TopicRepositoryInterface;
use App\Repositories\Contracts\UserTopicRepositoryInterface;
use App\Repositories\Contracts\UserAlertRepositoryInterface;

class UserService
{
    protected $userRepository;
    protected $alertRepository;
    protected $topicRepository;
    protected $userTopicRepository;
    protected $userAlertRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        AlertRepositoryInterface $alertRepository,
        TopicRepositoryInterface $topicRepository,
        UserTopicRepositoryInterface $userTopicRepository,
        UserAlertRepositoryInterface $userAlertRepository
    ) {
        $this->userRepository = $userRepository;
        $this->alertRepository = $alertRepository;
        $this->topicRepository = $topicRepository;
        $this->userTopicRepository = $userTopicRepository;
        $this->userAlertRepository = $userAlertRepository;
    }

    public function registerUser($name, $email)
    {
        $userExist = $this->userRepository->emailExist($email);

        if ($userExist) {
            throw new Exception("El usuario con email '{$email}' ya existe.");
        }
        $this->userRepository->createUser($name, $email);
    }

    public function subscribeUserToTopic($userId, $topicId)
    {
        $user = $this->userRepository->findUserById($userId);
        if (!$user) {
            throw new \Exception("Usuario no encontrado.");
        }

        $topic = $this->topicRepository->findTopicById($topicId);
        if (!$topic) {
            throw new \Exception("Tema no encontrado.");
        }

        $this->userTopicRepository->subscribeUserToTopic($topicId, $userId);
    }

    public function markUserAlertAsRead($userId, $alertId)
    {
        $user = $this->userRepository->findUserById($userId);
        if (!$user) {
            throw new \Exception("Usuario no encontrado.");
        }
        $alert = $this->alertRepository->getAlertById($alertId);
        if (!$alert) {
            throw new \Exception("Alerta no encontrada.");
        }

        $unreadAlerts = $this->userAlertRepository->getUnreadUserAlertsByUser($userId);
        foreach ($unreadAlerts as $userAlert) {
            if ($userAlert->getAlertId() == $alertId) {
                $userAlert->markAsRead();
            }
        }
    }
}
