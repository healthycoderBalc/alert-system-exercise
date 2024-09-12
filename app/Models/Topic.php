<?php

namespace App\Models;

class Topic
{
    private $id;
    private $name;

    public function __construct($name, $id = null)
    {
        $this->name = $name;
        $this->id = $id ?? uniqid();
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }
}
