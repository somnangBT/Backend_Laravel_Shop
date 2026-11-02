<?php

// Suppress deprecation warnings on PHP 8.4/8.5
error_reporting(E_ALL & ~E_DEPRECATED);

// Fix Symfony / Laravel determining BaseUrl as '/api' due to SCRIPT_NAME = '/api/index.php'
$_SERVER['SCRIPT_NAME'] = '/index.php';

require __DIR__ . "/../public/index.php";
