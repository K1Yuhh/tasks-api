<?php

declare(strict_types=1);

namespace K1\App;

final class Response
{

    public static function jsonDecode(string $json, bool $assocArray = true)
    {
        return json_decode($json, $assocArray);
    }

    public static function json(array $json): string
    {
        return json_encode($json);
    }

   public static function status(int $code = 200): Response
    {
        http_response_code($code);
        return (new Response());
    }

    public static function header(string $header): void
    {
        header("$header");
    }
}