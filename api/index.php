<?php

if (isset($_SERVER['HTTP_USER_AGENT']) && str_contains($_SERVER['HTTP_USER_AGENT'], 'PowerShell')) {
    header('Content-Type: application/json');
    echo json_encode($_SERVER, JSON_PRETTY_PRINT);
    exit;
}

// Suppress deprecation warnings on PHP 8.4/8.5
error_reporting(E_ALL & ~E_DEPRECATED);

require __DIR__ . "/../public/index.php";
