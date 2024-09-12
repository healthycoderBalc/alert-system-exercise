<?php

namespace Tests\Unit;

use App\Models\Topic;

use PHPUnit\Framework\TestCase;

class TopicTest extends TestCase
{
    public function testTopicCreation()
    {
        $name = 'Foto';
        $topic = new Topic($name);

        $this->assertNotNull($topic->getId());
        $this->assertEquals($name, $topic->getName());
    }
}
