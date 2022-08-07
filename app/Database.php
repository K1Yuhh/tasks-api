<?php

declare(strict_types=1);

namespace K1\App;

use PDO;

class Database
{
    private ?PDO $con = null;
    private string $host;
    private string $name;
    private string $user;
    private string $password;

    public function __construct()
    {
        $this->host     = $_ENV['DB_HOST'];
        $this->name     = $_ENV['DB_NAME'];
        $this->user     = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASS'];
    }

    public function getConnection(): PDO
    {
        if (is_null($this->con)) {
            $dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8";

            $this->con = new PDO($dsn, $this->user,$this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false
            ]);
        }
        return $this->con;
    }
}