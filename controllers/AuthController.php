<?php

namespace K1\Controllers;

use K1\App\Controller;
use K1\App\Request;
use K1\App\Response;
use K1\App\View;
use K1\Models\UserGateway;

class AuthController extends Controller
{
    protected UserGateway $userGateway;
    private array $payload;
    private string $accessToken;

    public function __construct()
    {
        parent::__construct();
        $this->userGateway = new UserGateway;
    }

    public function login(): string
    {
        $data = $this->getRequestBody();

        if (!array_key_exists('username', $data) || !array_key_exists('password', $data))
            return Response::status(400)->json(['msg' => 'Missing login credentials']);

        $user = $this->userGateway->checkUser($data['username']);

        if (!$user)
            return Response::status(401)->json(['msg' => 'Invalid Authentication']);

        if (!password_verify($data['password'], $user['password']))
            return Response::status(401)->json(['msg' => 'Invalid Authentication']);

        return Response::status(200)->json(['msg' => 'Successful Authentication', 'access_token' => $this->getPayload($user)]);
    }

    public function register(): string
    {
        if (Request::isPost()) {

            if (!isset($_POST['name']) || !isset($_POST['username']) || !isset($_POST['password']))
                return 'Ok';

            if ($this->userGateway->checkUser($_POST['username']))
                return 'User already exists';

            $create = $this->userGateway->createUser($_POST);

            return $create ? "Thanks for registering $create" : 'Something went wrong';
        }
        return View::render('Register');
    }

    private function getPayload(array $user): string
    {
        $this->payload = [
            'id' => $user['id'],
            'name' => $user['name']
        ];

        $this->accessToken = base64_encode(Response::json($this->payload));

        return $this->accessToken;
    }
}