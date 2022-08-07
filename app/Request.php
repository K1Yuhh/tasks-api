<?php

declare(strict_types=1);

namespace K1\App;

final class Request
{
    public static function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    public static function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public static function isDelete(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'DELETE';
    }

    public static function isPatch(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'PATCH';
    }

    public static function isPut(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'PUT';
    }
}