<?php

declare(strict_types=1);

namespace K1\Models;

use K1\App\Database;
use PDO;

class TaskGateway extends Database
{
    public function __construct()
    {
        parent::__construct();
        $this->con = $this->getConnection();
    }

    public function getAllForUser(int $user_id): array
    {
        $sql = "SELECT * FROM task WHERE user_id = :user_id ORDER BY name";

        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['is_completed'] = (bool) $row['is_completed'];
            $data[] = $row;
        }
        return $data;
    }

    public function getForUser(int $user_id, string $id): array|bool
    {
        $sql = "SELECT * FROM task WHERE user_id = :user_id AND id = :id";

        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $data['is_completed'] = (bool) $data['is_completed'];
        }
        return $data;
    }

    public function createForUser(int $user_id, array $data): string
    {
        $sql = "INSERT INTO task (name, priority, is_completed, user_id)";
        $sql .= "VALUES(:name, :priority, :is_completed, :user_id)";

        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(':priority', $data['priority' ?? null], PDO::PARAM_INT);
        $stmt->bindParam(':is_completed', $data['is_completed' ?? false], PDO::PARAM_BOOL);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $this->con->lastInsertId();
    }

    public function updateForUser(int $user_id, string $id, array $data): int
    {
        $fields = [];

        if (!empty($data['name'])) {
            $fields['name'] = [
                $data['name'],
                PDO::PARAM_STR
            ];
        }

        if (array_key_exists('priority', $data)) {
            $fields['priority'] = [
                $data['priority'],
                $data['priority'] === null ? PDO::PARAM_NULL : PDO::PARAM_INT
            ];
        }

        if (array_key_exists('is_completed', $data)) {
            $fields['is_completed'] = [
                $data['is_completed'],
                PDO::PARAM_BOOL
            ];
        }

        if (empty($fields))
            return 0;

        $sets = array_map(function ($value) {
            return "$value = :$value";
        }, array_keys($fields));

        $sql = "UPDATE task" . " SET " . implode(", ", $sets) . " WHERE user_id = :user_id" . " AND id = :id";

        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        foreach ($fields as $name => $value) {
            $stmt->bindParam("$name", $value[0], $value[1]);
        }

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function deleteForUser(int $user_id, string $id): int
    {
        $sql = "DELETE FROM task WHERE user_id = :user_id AND id = :id";

        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}