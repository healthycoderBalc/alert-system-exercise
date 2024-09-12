<?php

namespace App\Services;

use Exception;


use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\AlertRepositoryInterface;
use App\Repositories\Contracts\TopicRepositoryInterface;
use App\Repositories\Contracts\UserTopicRepositoryInterface;
use App\Repositories\Contracts\UserAlertRepositoryInterface;
use App\Models\Alert;
use App\Models\User;

class AlertService
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

    public function createAndSendAlert(Alert $alert, User $user = null)
    {
        $this->alertRepository->createAlert($alert);
        $this->sendAlertToUsers($alert, $user);
    }

    private function sendAlertToUsers(Alert $alert, User $user = null)
    {
        if ($user) {
            $userExist = $this->userRepository->findUserById($user->getId()) ?? null;
            if (!$userExist) {
                throw new Exception("El usuario con email '{$user->getEmail()}' no existe.");
            }
            $this->userAlertRepository->createUserAlert($alert->getId(), $user->getId());
        } else {
            $usersSubscribedToTopic = $this->userTopicRepository->getUsersByTopicId($alert->getTopicId());

            foreach ($usersSubscribedToTopic as $userSubscribed) {
                $this->userAlertRepository->createUserAlert($alert->getId(), $userSubscribed->getId());
            }
        }
    }

    public function getUnreadUnexpiredAlertsByUser($userId)
    {
        $alerts = $this->alertRepository->getUnreadUnexpiredAlertsByUser($userId);
        return $this->sortAlertsByTypeAndDate($alerts);
    }

    public function getUnexpiredAlertsByTopic($topicId)
    {
        $alertsByTopic = $this->alertRepository->getAlertsByTopic($topicId);
        $orderedAlertsByTopic = $this->sortAlertsByTypeAndDate($alertsByTopic);

        $result = [];
        foreach ($orderedAlertsByTopic as $alert) {
            $alertId = $alert->getId();
            $isGlobal = $this->alertIsGlobal($alertId);

            $result[] = [
                'alerta' => $alert,
                'esGlobal' => $isGlobal
            ];
        }

        return $result;
    }

    public function sortAlertsByTypeAndDate($alerts)
    {
        if (!is_array($alerts)) {
            return [];
        }

        $urgentAlerts = [];
        $informativeAlerts = [];

        foreach ($alerts as $alert) {
            if ($alert->isUrgent()) {
                $urgentAlerts[] = $alert;
            } else if ($alert->isInformative()) {
                $informativeAlerts[] = $alert;
            } else {
                throw new Exception("La alerta no es urgente ni informativa");
            }
        }

        usort($urgentAlerts, function ($a, $b) {
            return $b->getCreatedAt() <=> $a->getCreatedAt();
        });

        usort($informativeAlerts, function ($a, $b) {
            return $a->getCreatedAt() <=> $b->getCreatedAt();
        });

        return array_merge($urgentAlerts, $informativeAlerts);
    }

    public function alertIsGlobal($alertId)
    {
        $userAlertsByAlertId = $this->userAlertRepository->getUserAlertsByAlertId($alertId);

        $count = is_array($userAlertsByAlertId) ? count($userAlertsByAlertId) : 0;

        return $count > 1;
    }
}
