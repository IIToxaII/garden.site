<?php

namespace App\db;

class DBConfig
{
    public $dns;
    public $username;
    public $password;

    public function __construct(array $config)
    {
        $this->dns = $config['dns'];
        $this->username = $config['username'];
        $this->password = $config['password'];
    }
}