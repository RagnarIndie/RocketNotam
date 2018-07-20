<?php

use Symfony\Component\Debug\Debug;

$loader = require_once BASE_PATH.'vendor/autoload.php';
Debug::enable();

$app = require __DIR__.'/../app/bootstrap_web.php';
$app->run();
