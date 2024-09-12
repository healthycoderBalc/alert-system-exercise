<?php

namespace Tests\Unit;

use App\Models\UserTopic;
use PHPUnit\Framework\TestCase;

class UserTopicTest extends TestCase
{
    public function testUserTopicCreation()
    {
        $topicId = 'topic-123';
        $userId = 'user-456';
        $userTopic = new UserTopic($topicId, $userId);

        $this->assertNotNull($userTopic->getId());
        $this->assertEquals($topicId, $userTopic->getTopicId());
        $this->assertEquals($userId, $userTopic->getUserId());
    }
}
