<?php

require_once './system/Autoloader.php';
require_once './routes.php';
require_once './system/config.php';
require_once './system/ignoreconfig.php';

$uri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

defineRoutes($uri, $requestMethod);
