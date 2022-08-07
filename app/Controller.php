<?php

namespace K1\App;

use K1\Models\UserGateway;

abstract class Controller
{
    protected int $user_id;
    protected UserGateway $userGateway;

    public function __construct()
    {
        $this->userGateway = new UserGateway;
    }

    protected function getRequestBody(): array
    {
        return (array) json_decode(file_get_contents("php://input"), true);
    }

    protected function checkAuthenticationApiKeyAndToken(): bool
    {
        return $this->authenticateApiKey() || $this->authenticateAccessToken();
    }

    protected function authenticateApiKey(): bool
    {
        if (!isset($_SERVER['HTTP_X_API_KEY']))
            return false;

        $user = $this->userGateway->getByAPIKey($_SERVER['HTTP_X_API_KEY']);

        if (!$user)
            return false;

        $this->user_id = $user['id'];

        return true;
    }

    protected function authenticateAccessToken(): bool
    {
        if (!isset($_SERVER['HTTP_AUTHORIZATION']))
            return false;

        if(!preg_match("/^Bearer\s+(.*)$/", $_SERVER['HTTP_AUTHORIZATION'], $matches))
            return false;

        $convertToPlainText = base64_decode($matches[1], true);

        if(!$convertToPlainText)
            return false;

        $data = Response::jsonDecode($convertToPlainText, true);

        if (is_null($data))
            return false;

        $this->user_id = $data['id'];

        return true;
    }

    protected function isValidID(): bool
    {
        # Checks if ID isset
        if (!isset($_GET['id']))
            return false;

        # Checks if ID is numeric
        return is_numeric($_GET['id']);
    }
}