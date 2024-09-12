<?php

namespace App\Services;

use App\Repositories\Contracts\TopicRepositoryInterface;
use Exception;

class TopicService
{
    protected $topicRepository;

    public function __construct(TopicRepositoryInterface $topicRepository)
    {
        $this->topicRepository = $topicRepository;
    }

    public function retisterTopic($name)
    {
        $existingTopic = $this->topicRepository->findByName($name);

        if ($existingTopic) {
            throw new Exception("El tópico con nombre '{$name}' ya existe.");
        }
        $this->topicRepository->createTopic($name);
    }
}
