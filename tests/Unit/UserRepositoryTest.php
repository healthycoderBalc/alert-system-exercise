<?php

namespace Tests\Unit;

use App\Models\User;
use App\Repositories\Contracts\TopicRepositoryInterface;
use App\Repositories\Implementations\TopicRepository;
use App\Repositories\Implementations\UserRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    private $userRepository;
    private MockObject $topicRepositoryMock;

    protected function setUp(): void
    {
        $this->topicRepositoryMock = $this->createMock(TopicRepositoryInterface::class);

        $this->userRepository = new UserRepository($this->topicRepositoryMock instanceof TopicRepositoryInterface ? $this->topicRepositoryMock : null);
    }

    public function testCreateUser()
    {
        $name = 'Jane Doe';
        $email = 'jane.doe@example.com';

        $user = $this->userRepository->createUser($name, $email);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($name, $user->getName());
        $this->assertEquals($email, $user->getEmail());
    }
}
