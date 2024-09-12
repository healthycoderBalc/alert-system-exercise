<?php

namespace Tests\Unit\Services;

use App\Services\AlertService;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\AlertRepositoryInterface;
use App\Repositories\Contracts\TopicRepositoryInterface;
use App\Repositories\Contracts\UserTopicRepositoryInterface;
use App\Repositories\Contracts\UserAlertRepositoryInterface;
use App\Repositories\Implementations\UserAlertRepository;
use App\Models\Alert;
use App\Models\User;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use DateTime;

class AlertServiceTest extends TestCase
{
    private $alertService;
    private MockObject $userRepositoryMock;
    private MockObject $alertRepositoryMock;
    private MockObject $topicRepositoryMock;
    private MockObject $userTopicRepositoryMock;
    private MockObject $userAlertRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->alertRepositoryMock = $this->createMock(AlertRepositoryInterface::class);
        $this->topicRepositoryMock = $this->createMock(TopicRepositoryInterface::class);
        $this->userTopicRepositoryMock = $this->createMock(UserTopicRepositoryInterface::class);
        $this->userAlertRepositoryMock = $this->createMock(UserAlertRepositoryInterface::class);

        $this->alertService = new AlertService(
            $this->userRepositoryMock instanceof UserRepositoryInterface ? $this->userRepositoryMock : null,
            $this->alertRepositoryMock
                instanceof AlertRepositoryInterface ? $this->alertRepositoryMock : null,
            $this->topicRepositoryMock
                instanceof TopicRepositoryInterface ? $this->topicRepositoryMock : null,
            $this->userTopicRepositoryMock
                instanceof UserTopicRepositoryInterface ? $this->userTopicRepositoryMock : null,
            $this->userAlertRepositoryMock
                instanceof UserAlertRepositoryInterface ? $this->userAlertRepositoryMock : null
        );

        $this->clearUserAlerts();
    }

    protected function clearUserAlerts(): void
    {
        $reflection = new \ReflectionClass(UserAlertRepository::class);
        $userTopicsProperty = $reflection->getProperty('userAlerts');
        $userTopicsProperty->setAccessible(true);
        $userTopicsProperty->setValue([]);
    }


    public function testCreateAndSendAlertToUser()
    {
        $alert = new Alert('Test Alert', 'topic-1', Alert::ALERT_TYPE_INFORMATIVE, new \DateTime());
        $user = new User('user-1', 'user@example.com');

        $this->userRepositoryMock->expects($this->once())
            ->method('findUserById')
            ->with($user->getId())
            ->willReturn($user);

        $this->alertRepositoryMock->expects($this->once())
            ->method('createAlert')
            ->with($alert);

        $this->userAlertRepositoryMock->expects($this->once())
            ->method('createUserAlert')
            ->with($alert->getId(), $user->getId());

        $this->alertService->createAndSendAlert($alert, $user);
    }

    public function testCreateAndSendAlertToAllUsers()
    {
        $alert = new Alert('Test Alert', 'topic-1', Alert::ALERT_TYPE_INFORMATIVE, new \DateTime());

        $user1 = new User('user-1', 'user1@example.com');
        $user2 = new User('user-2', 'user2@example.com');

        $this->userTopicRepositoryMock->expects($this->once())
            ->method('getUsersByTopicId')
            ->with($alert->getTopicId())
            ->willReturn([$user1, $user2]);

        $this->alertRepositoryMock->expects($this->once())
            ->method('createAlert')
            ->with($alert);

        $this->userAlertRepositoryMock->expects($this->exactly(2))
            ->method('createUserAlert')
            ->willReturnCallback(function ($alertId, $userId) use ($user1, $user2, $alert) {
                static $calls = 0;
                $calls++;

                if ($calls === 1) {
                    $this->assertEquals($alert->getId(), $alertId);
                    $this->assertEquals($user1->getId(), $userId);
                } elseif ($calls === 2) {
                    $this->assertEquals($alert->getId(), $alertId);
                    $this->assertEquals($user2->getId(), $userId);
                }
            });

        $this->alertService->createAndSendAlert($alert);
    }


    public function testGetUnreadUnexpiredAlertsByUser()
    {
        $userId = 'user-1';
        $alert = new Alert('Test Alert', 'topic-1', Alert::ALERT_TYPE_INFORMATIVE, new \DateTime());
        $alerts = [$alert];

        $this->alertRepositoryMock->expects($this->once())
            ->method('getUnreadUnexpiredAlertsByUser')
            ->with($userId)
            ->willReturn($alerts);

        $this->alertService = $this->getMockBuilder(AlertService::class)
            ->setConstructorArgs([
                $this->userRepositoryMock,
                $this->alertRepositoryMock,
                $this->topicRepositoryMock,
                $this->userTopicRepositoryMock,
                $this->userAlertRepositoryMock
            ])
            ->onlyMethods(['sortAlertsByTypeAndDate'])
            ->getMock();

        $sortedAlerts = [$alert];

        $this->alertService->expects($this->once())
            ->method('sortAlertsByTypeAndDate')
            ->with($alerts)
            ->willReturn($sortedAlerts);

        $result = $this->alertService->getUnreadUnexpiredAlertsByUser($userId);

        $this->assertEquals($sortedAlerts, $result);
    }


    public function testGetUnexpiredAlertsByTopic()
    {
        $topicId = 'topic-1';
        $alert = new Alert('Test Alert', $topicId, Alert::ALERT_TYPE_INFORMATIVE, new \DateTime());
        $alerts = [$alert];

        $this->alertRepositoryMock->expects($this->once())
            ->method('getAlertsByTopic')
            ->with($topicId)
            ->willReturn($alerts);

        $this->alertService = $this->getMockBuilder(AlertService::class)
            ->setConstructorArgs([
                $this->userRepositoryMock,
                $this->alertRepositoryMock,
                $this->topicRepositoryMock,
                $this->userTopicRepositoryMock,
                $this->userAlertRepositoryMock
            ])
            ->onlyMethods(['sortAlertsByTypeAndDate', 'alertIsGlobal'])
            ->getMock();

        $sortedAlerts = [$alert];
        $this->alertService->expects($this->once())
            ->method('sortAlertsByTypeAndDate')
            ->with($alerts)
            ->willReturn($sortedAlerts);

        $this->alertService->expects($this->once())
            ->method('alertIsGlobal')
            ->with($alert->getId())
            ->willReturn(true);

        $result = $this->alertService->getUnexpiredAlertsByTopic($topicId);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('alerta', $result[0]);
        $this->assertArrayHasKey('esGlobal', $result[0]);
        $this->assertTrue($result[0]['esGlobal']);
    }

    public function testSortAlertsByTypeAndDate()
    {
        $alert1 = new Alert('I1', '1', Alert::ALERT_TYPE_INFORMATIVE, (new DateTime())->modify('-6 hours'));
        $alert2 = new Alert('I2', '1', Alert::ALERT_TYPE_INFORMATIVE, (new DateTime())->modify('-5 hours'));
        $alert3 = new Alert('U1', '1', Alert::ALERT_TYPE_URGENT, (new DateTime())->modify('-4 hours'));
        $alert4 = new Alert('I3', '1', Alert::ALERT_TYPE_INFORMATIVE, (new DateTime())->modify('-3 hours'));
        $alert5 = new Alert('U2', '1', Alert::ALERT_TYPE_URGENT, (new DateTime())->modify('-2 hours'));
        $alert6 = new Alert('I4', '1', Alert::ALERT_TYPE_INFORMATIVE, (new DateTime())->modify('-1 hour'));

        // Unsorted alerts
        $alerts = [$alert1, $alert2, $alert3, $alert4, $alert5, $alert6];

        $this->alertRepositoryMock->method('getAlertsByTopic')->willReturn($alerts);

        $sortedAlerts = $this->alertService->getUnexpiredAlertsByTopic('1');

        var_dump($sortedAlerts);

        // Verify order
        $this->assertEquals('U2', $sortedAlerts[0]['alerta']->getDescription());
        $this->assertEquals('U1', $sortedAlerts[1]['alerta']->getDescription());
        $this->assertEquals('I1', $sortedAlerts[2]['alerta']->getDescription());
        $this->assertEquals('I2', $sortedAlerts[3]['alerta']->getDescription());
        $this->assertEquals('I3', $sortedAlerts[4]['alerta']->getDescription());
        $this->assertEquals('I4', $sortedAlerts[5]['alerta']->getDescription());
    }
}
