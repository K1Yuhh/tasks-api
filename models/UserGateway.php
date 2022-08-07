<?php

namespace K1\Models;

use K1\App\Database;
use K1\App\Response;
use PDO;

class UserGateway extends Database
{
    public function __construct()
    {
        parent::__construct();
        $this->con = $this->getConnection();
    }

    public function getByAPIKey(string $key): array|bool
    {
        $sql = "SELECT * FROM users WHERE api_key = :api_key";

        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':api_key', $key, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkUser(string $username): array|bool
    {
        $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";

        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser(array $data): string|bool
    {
        $api_key = bin2hex(random_bytes(16));

        $sql = "INSERT INTO users (name, username, password, api_key)";
        $sql .= "VALUES (:name, :username, :password, :api_key)";

        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(':username', $data['username'], PDO::PARAM_STR);
        $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT), PDO::PARAM_STR);
        $stmt->bindValue(':api_key', $api_key, PDO::PARAM_STR);

        return $stmt->execute() ? $api_key : false;
    }
}