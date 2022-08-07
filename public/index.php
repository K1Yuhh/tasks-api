<?php

declare(strict_types=1);

use K1\App\Router;

require_once __DIR__. '/../vendor/autoload.php';

set_error_handler("\K1\App\ErrorHandler::handleError");
set_exception_handler("\\K1\\App\\ErrorHandler::handleException");

$router = new Router();

$router->add(['get', 'post', 'delete', 'patch'],"/task", 'Task::getSingleTask');
$router->add(['get', 'post'],"/tasks/all", 'Task::getAllTasks');
$router->add(['get', 'post'], "/register", 'Auth::register');
$router->add(['post'], "/login", 'Auth::login');

$router->resolve();