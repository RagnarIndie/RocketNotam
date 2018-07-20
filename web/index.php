<?php
define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', __DIR__.'/../');
define('APP_SRC_PATH', BASE_PATH.'src/App/');
define('LIB_PATH', APP_SRC_PATH.'Library/');

$loader = require_once BASE_PATH.'vendor/autoload.php';
$app = require BASE_PATH.'app/bootstrap_web.php';

$app->run();
