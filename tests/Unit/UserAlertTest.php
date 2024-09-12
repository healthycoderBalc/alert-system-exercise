<?php

use App\Models\UserAlert;
use PHPUnit\Framework\TestCase;

class UserAlertTest extends TestCase
{
    public function testUserAlertCreation()
    {
        $alertId = 'alert-123';
        $userId = 'user-456';
        $userAlert = new UserAlert($alertId, $userId);

        $this->assertNotNull($userAlert->getId());
        $this->assertEquals($alertId, $userAlert->getAlertId());
        $this->assertEquals($userId, $userAlert->getUserId());
        $this->assertFalse($userAlert->isRead());
        $this->assertNull($userAlert->getReadAt());
    }

    public function testMarkAsRead()
    {
        $alertId = 'alert-123';
        $userId = 'user-456';
        $userAlert = new UserAlert($alertId, $userId);

        $this->assertFalse($userAlert->isRead());
        $this->assertNull($userAlert->getReadAt());

        $userAlert->markAsRead();

        $this->assertTrue($userAlert->isRead());
        $this->assertNotNull($userAlert->getReadAt());
        $this->assertInstanceOf(\DateTime::class, $userAlert->getReadAt());
    }
}
