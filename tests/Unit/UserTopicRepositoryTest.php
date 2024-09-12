<?php

namespace Tests\Unit;

use App\Models\UserTopic;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\TopicRepositoryInterface;
use App\Repositories\Implementations\UserTopicRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserTopicRepositoryTest extends TestCase
{
    private $userTopicRepository;
    private MockObject $userRepositoryMock;
    private MockObject $topicRepositoryMock;

    protected function setUp(): void
    {
        $this->userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->topicRepositoryMock = $this->createMock(TopicRepositoryInterface::class);

        $this->userTopicRepository = new UserTopicRepository($this->userRepositoryMock instanceof UserRepositoryInterface ? $this->userRepositoryMock : null, $this->topicRepositoryMock instanceof TopicRepositoryInterface ? $this->topicRepositoryMock : null);
        $this->clearUserTopics();
    }

    protected function clearUserTopics(): void
    {
        $reflection = new \ReflectionClass(UserTopicRepository::class);
        $userTopicsProperty = $reflection->getProperty('userTopics');
        $userTopicsProperty->setAccessible(true);
        $userTopicsProperty->setValue([]);
    }

    public function testSubscribeUserToTopic()
    {
        $topicId = 'topic-1';
        $userId = 'user-1';

        $this->topicRepositoryMock->expects($this->once())
            ->method('findTopicById')
            ->with($topicId)
            ->willReturn(true);

        $this->userRepositoryMock->expects($this->once())
            ->method('findUserById')
            ->with($userId)
            ->willReturn(true);

        $userTopic = $this->userTopicRepository->subscribeUserToTopic($topicId, $userId);

        $this->assertInstanceOf(UserTopic::class, $userTopic);
        $this->assertEquals($topicId, $userTopic->getTopicId());
        $this->assertEquals($userId, $userTopic->getUserId());
    }

    public function testSubscribeUserToTopicThrowsExceptionIfTopicDoesNotExist()
    {
        $topicId = 'topic-1';
        $userId = 'user-1';

        $this->topicRepositoryMock->expects($this->once())
            ->method('findTopicById')
            ->with($topicId)
            ->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("El tema ID: {$topicId} no existe.");

        $this->userTopicRepository->subscribeUserToTopic($topicId, $userId);
    }

    public function testSubscribeUserToTopicThrowsExceptionIfUserDoesNotExist()
    {
        $topicId = 'topic-1';
        $userId = 'user-1';

        $this->topicRepositoryMock->expects($this->once())
            ->method('findTopicById')
            ->with($topicId)
            ->willReturn(true);

        $this->userRepositoryMock->expects($this->once())
            ->method('findUserById')
            ->with($userId)
            ->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("El usuario ID: {$userId} no existe.");

        $this->userTopicRepository->subscribeUserToTopic($topicId, $userId);
    }
}
