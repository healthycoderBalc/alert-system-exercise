<?php

namespace Tests\Unit;

use App\Models\UserAlert;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Implementations\UserAlertRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserAlertRepositoryTest extends TestCase
{
    private $userAlertRepository;
    private MockObject $userRepositoryMock;

    protected function setUp(): void
    {
        $this->userRepositoryMock = $this->createMock(UserRepositoryInterface::class);

        $this->userAlertRepository = new UserAlertRepository($this->userRepositoryMock instanceof UserRepositoryInterface ? $this->userRepositoryMock : null);
        $this->clearUserAlerts();
    }

    protected function clearUserAlerts(): void
    {
        $reflection = new \ReflectionClass(UserAlertRepository::class);
        $userAlertsProperty = $reflection->getProperty('userAlerts');
        $userAlertsProperty->setAccessible(true);
        $userAlertsProperty->setValue([]);
    }

    public function testCreateUserAlert()
    {
        $alertId = 'alert-1';
        $userId = 'user-1';

        $userAlert = $this->userAlertRepository->createUserAlert($alertId, $userId);

        $this->assertInstanceOf(UserAlert::class, $userAlert);
        $this->assertEquals($alertId, $userAlert->getAlertId());
        $this->assertEquals($userId, $userAlert->getUserId());
    }

    public function testGetAllUserAlerts()
    {
        $alertId1 = 'alert-1';
        $userId1 = 'user-1';
        $alertId2 = 'alert-2';
        $userId2 = 'user-2';

        $this->userAlertRepository->createUserAlert($alertId1, $userId1);
        $this->userAlertRepository->createUserAlert($alertId2, $userId2);

        $userAlerts = $this->userAlertRepository->getAllUserAlerts();

        $this->assertCount(2, $userAlerts);
        $this->assertInstanceOf(UserAlert::class, $userAlerts[0]);
        $this->assertInstanceOf(UserAlert::class, $userAlerts[1]);
    }

    public function testGetById()
    {
        $alertId = 'alert-1';
        $userId = 'user-1';
        $userAlert = $this->userAlertRepository->createUserAlert($alertId, $userId);

        $retrievedUserAlert = $this->userAlertRepository->getById($userAlert->getId());

        $this->assertInstanceOf(UserAlert::class, $retrievedUserAlert);
        $this->assertEquals($userAlert->getId(), $retrievedUserAlert->getId());
    }

    public function testGetUnreadUserAlertsByUser()
    {
        $userId = 'user-1';
        $unreadUserAlert = $this->userAlertRepository->createUserAlert('alert-2', $userId);
        $readUserAlert = $this->userAlertRepository->createUserAlert('alert-1', $userId);

        // Simulate that the first alert has been read
        $reflection = new \ReflectionClass($readUserAlert);
        $readProperty = $reflection->getProperty('read');
        $readProperty->setAccessible(true);
        $readProperty->setValue($readUserAlert, true);

        $unreadUserAlerts = $this->userAlertRepository->getUnreadUserAlertsByUser($userId);

        $this->assertCount(1, $unreadUserAlerts);
        $this->assertEquals('alert-2', $unreadUserAlerts[0]->getAlertId());
    }

    public function testGetUserAlertsByAlertId()
    {
        $alertId = 'alert-1';
        $userId1 = 'user-1';
        $userId2 = 'user-2';

        $this->userAlertRepository->createUserAlert($alertId, $userId1);
        $this->userAlertRepository->createUserAlert($alertId, $userId2);

        $userAlerts = $this->userAlertRepository->getUserAlertsByAlertId($alertId);

        $this->assertCount(2, $userAlerts);
        $this->assertEquals($alertId, $userAlerts[0]->getAlertId());
        $this->assertEquals($alertId, $userAlerts[1]->getAlertId());
    }
}
