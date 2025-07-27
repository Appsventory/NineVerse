<?php

if (!function_exists('redirect')) {
    function redirect(string $url)
    {
        header("Location: $url");
        exit;
    }
}
