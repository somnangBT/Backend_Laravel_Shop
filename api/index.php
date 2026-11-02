<?php

if (isset($_GET['debug_env'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? null,
        'PATH_INFO' => $_SERVER['PATH_INFO'] ?? null,
        'PHP_SELF' => $_SERVER['PHP_SELF'] ?? null,
        'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'] ?? null,
        'HTTP_X_FORWARDED_URI' => $_SERVER['HTTP_X_FORWARDED_URI'] ?? null,
        'HTTP_X_VERCEL_FORWARDED_FOR' => $_SERVER['HTTP_X_VERCEL_FORWARDED_FOR'] ?? null,
    ], JSON_PRETTY_PRINT);
    exit;
}

// Suppress deprecation warnings on PHP 8.4/8.5
error_reporting(E_ALL & ~E_DEPRECATED);

require __DIR__ . "/../public/index.php";
