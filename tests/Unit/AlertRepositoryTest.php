<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Repositories\Implementations\AlertRepository;
use App\Repositories\Contracts\UserAlertRepositoryInterface;
use App\Models\Alert;
use App\Models\UserAlert;
use DateTime;

class AlertRepositoryTest extends TestCase
{
    private $alertRepository;
    private $userAlertRepositoryMock;

    protected function setUp(): void
    {
        $this->userAlertRepositoryMock = $this->createMock(UserAlertRepositoryInterface::class);

        $this->alertRepository = new AlertRepository($this->userAlertRepositoryMock instanceof UserAlertRepositoryInterface ? $this->userAlertRepositoryMock : null);

        $this->clearAlerts();
    }

    protected function clearAlerts(): void
    {
        $reflection = new \ReflectionClass(AlertRepository::class);
        $alertsProperty = $reflection->getProperty('alerts');
        $alertsProperty->setAccessible(true);
        $alertsProperty->setValue([]);
    }

    public function testCreateAlert()
    {
        $description = 'Important update!';
        $topicId = '1';
        $alertTypeId = Alert::ALERT_TYPE_INFORMATIVE;
        $createdAt = new DateTime();
        $alert = new Alert($description, $topicId, $alertTypeId, $createdAt);

        $this->alertRepository->createAlert($alert);

        $retrievedAlert = $this->alertRepository->getAlertById($alert->getId());
        $this->assertInstanceOf(Alert::class, $retrievedAlert);
        $this->assertEquals($description, $retrievedAlert->getDescription());
    }

    public function testGetUnreadUnexpiredAlertsByUser()
    {
        // Simulate Unread Alerts
        $userId = 'user-123';
        $userAlert1 = new UserAlert('alert-1', $userId);
        $userAlert2 = new UserAlert('alert-2', $userId);

        // Mock will return unread alerts
        $this->userAlertRepositoryMock->expects($this->once())
            ->method('getUnreadUserAlertsByUser')
            ->with($userId)
            ->willReturn([$userAlert1, $userAlert2]);

        // Create two alerts: one expired, one unexpired
        $alert1 = new Alert('Alert 1', '1', Alert::ALERT_TYPE_INFORMATIVE, new DateTime('-2 days'), new DateTime('-1 day'), 'alert-1');
        $alert2 = new Alert('Alert 2', '1', Alert::ALERT_TYPE_URGENT, new DateTime('-2 days'), new DateTime('+1 day'), 'alert-2');

        $this->alertRepository->createAlert($alert1);
        $this->alertRepository->createAlert($alert2);

        // Get unread and unexpired alerts
        $unreadUnexpiredAlerts = $this->alertRepository->getUnreadUnexpiredAlertsByUser($userId);

        // only alert2 should be unread and unexpired
        $this->assertCount(1, $unreadUnexpiredAlerts);
        $this->assertEquals('Alert 2', $unreadUnexpiredAlerts[0]->getDescription());
    }

    public function testGetAlertsByTopic()
    {
        $topicId = '1';
        $activeAlert = new Alert('Active alert', $topicId, Alert::ALERT_TYPE_URGENT, new DateTime('-1 day'), new DateTime('+1 day'));
        $expiredAlert = new Alert('Expired alert', $topicId, Alert::ALERT_TYPE_INFORMATIVE, new DateTime('-5 days'), new DateTime('-1 day'));


        $this->alertRepository->createAlert($activeAlert);
        $this->alertRepository->createAlert($expiredAlert);

        // Get unexpired alerts by topic
        $alerts = $this->alertRepository->getAlertsByTopic($topicId);

        // only active alert is returned
        $this->assertCount(1, $alerts);
        $this->assertEquals('Active alert', $alerts[0]->getDescription());
    }
}
