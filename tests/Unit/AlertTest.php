<?php

namespace Tests\Unit;

use App\Models\Alert;
use DateTime;

use PHPUnit\Framework\TestCase;

class AlertTest extends TestCase
{
    public function testAlertCreation()
    {
        $description = 'New technology alert!';
        $topicId = '1';
        $alertTypeId = Alert::ALERT_TYPE_URGENT;
        $createdAt = new DateTime();
        $alert = new Alert($description, $topicId, $alertTypeId, $createdAt);

        $this->assertNotNull($alert->getId());
        $this->assertEquals($description, $alert->getDescription());
        $this->assertEquals($topicId, $alert->getTopicId());
        $this->assertEquals($alertTypeId, $alert->getAlertTypeId());
        $this->assertEquals($createdAt, $alert->getCreatedAt());
    }

    public function testAlertExpiration()
    {
        $description = 'Expiring alert!';
        $topicId = '1';
        $alertTypeId = Alert::ALERT_TYPE_INFORMATIVE;
        $createdAt = (new DateTime())->modify('-2 days');
        $expirationDate = (new DateTime())->modify('-1 day');
        $alert = new Alert($description, $topicId, $alertTypeId, $createdAt, $expirationDate);

        $this->assertTrue($alert->isExpired());
    }
}
