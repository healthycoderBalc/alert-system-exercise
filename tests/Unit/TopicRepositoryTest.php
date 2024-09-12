<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Repositories\Implementations\TopicRepository;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Models\Topic;

class TopicRepositoryTest extends TestCase
{
    private $topicRepository;
    private $userRepositoryMock;

    protected function setUp(): void
    {
        $this->userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->topicRepository = new TopicRepository($this->userRepositoryMock instanceof UserRepositoryInterface ? $this->userRepositoryMock : null);
    }

    public function testCreateTopic()
    {
        $name = 'Health';
        $topic = $this->topicRepository->createTopic($name);

        $this->assertInstanceOf(Topic::class, $topic);
        $this->assertEquals($name, $topic->getName());
    }

    public function testFindTopicByName()
    {
        $name = 'Finance';
        $this->topicRepository->createTopic($name);

        $topic = $this->topicRepository->findByName($name);

        $this->assertInstanceOf(Topic::class, $topic);
        $this->assertEquals($name, $topic->getName());
    }
}
