<?php

namespace App\Models;

class User
{
    private $id;
    private $name;
    private $email;

    public function __construct(
        $name,
        $email,
        $id = null
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->id = $id ?? uniqid();
    }

    // Getters
    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getId()
    {
        return $this->id;
    }
}
