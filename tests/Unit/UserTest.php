<?php

namespace Tests\Unit;

use App\Models\User;

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserCreation()
    {
        $name = 'John Doe';
        $email = 'john.doe@example.com';
        $user = new User($name, $email);

        $this->assertNotNull($user->getId());
        $this->assertEquals($name, $user->getName());
        $this->assertEquals($email, $user->getEmail());
    }
}
