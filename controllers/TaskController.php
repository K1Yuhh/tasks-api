<?php

namespace K1\Controllers;

use K1\App\Controller;
use K1\App\Request;
use K1\App\Response;
use K1\Models\TaskGateway;

class TaskController extends Controller
{
    private array $errors = [];
    private TaskGateway $gateway;

    public function __construct()
    {
        parent::__construct();
        $this->gateway = new TaskGateway;
    }

    public function getSingleTask(): string
    {
        #Checks for Valid API-KEY
        if (!$this->checkAuthenticationApiKeyAndToken())
            return Response::status(401)->json(['msg' => 'Invalid API Key']);

        # Checks for Valid ID
        if (!$this->isValidID())
            return Response::status(400)->json(['msg' => 'ID Needs to be specified and numeric']);

        # Checks if task exists
        $task = $this->gateway->getForUser($this->user_id, $_GET['id']);

        # If task is not found
        if (!$task)
            return Response::status(404)->json(['msg' => "Task with ID {$_GET['id']} not found"]);

        # Checks if request method is POST and executes the given function
        if(Request::isPost())
            return 'Post';

        # Checks if request method is PUT and executes the given function
        if (Request::isPatch())
            return $this->updateTaskByID();

        # Checks if request method is DELETE and executes the given function
        if (Request::isDelete())
            return $this->deleteTaskByID();

        # If none of above return true the default request method will be GET
        return $this->showTaskByID($task);
    }

    public function getAllTasks(): string
    {
        if (!$this->checkAuthenticationApiKeyAndToken())
            return Response::status(401)->json(['msg' => 'Invalid API Key']);

        if (Request::isGet())
            return Response::json($this->gateway->getAllForUser($this->user_id));

        return $this->createTask();
    }

    public function createTask(): string
    {
        # Create a record in the database
        $data = $this->getRequestBody();
        $errors = $this->getValidationErrors($data);

        if (!empty($errors))
            return Response::status(422)->json(['Errors' => $errors]);

        $id = $this->gateway->createForUser($this->user_id, $data);
        return Response::status(201)->json(['msg' => 'Task created', 'id' => $id]);
    }

    public function showTaskByID(array $task): string
    {
        # Returns task
        return Response::json($task);
    }

    public function deleteTaskByID(): string
    {
        $rows = $this->gateway->deleteForUser($this->user_id, $_GET['id']);

        return Response::status()->json(['msg' => 'Task deleted', 'rows', $rows]);
    }

    public function updateTaskByID(): string
    {
        # Edits the task based on ID
        $data = $this->getRequestBody();
        $errors = $this->getValidationErrors($data, false);

        if (!empty($errors))
            return Response::status(422)->json(['Errors' => $errors]);

        $rows = $this->gateway->updateForUser($this->user_id, $_GET['id'], $data);
        return Response::status(201)->json(['msg' => 'Task updated', "Rows" => $rows]);
    }

    private function getValidationErrors(array $data, bool $isNew = true): array
    {
        if ($isNew && empty($data['name']))
            $this->errors[] = 'Name is required';

        if (!empty($data['priority']) && !is_int($data['priority'])) {
            $this->errors[] = "Priority must be an integer";
        }
        return $this->errors;
    }
}