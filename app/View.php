<?php

namespace K1\App;

class View
{
    public static function render(string $view, array $args = []): string
    {
        Response::header("Content-Type: text/html; charset=UTF-8");

        $file = "../Views/$view.php";

        if (!file_exists($file))
            return "'$file' does not exist";

        ob_start();
        require_once $file;
        return ob_get_clean();
    }
}