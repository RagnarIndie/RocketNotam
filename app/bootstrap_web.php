<?php
use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\SessionServiceProvider;

$loader->add('App', BASE_PATH.'src');

//Application Container
$app = new Application();

$app['getEnv'] = $app->protect(function () {
    $env = 'local';

    switch ($_SERVER['SERVER_NAME']) {
        case 'test-rocket.hubrockvoid.com':
            $env = 'prod';
            break;
    }

    return $env;
});

$app['show_debug'] = $app->protect(function ($data) {
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
});

if ($app['getEnv']() == 'local') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

//Include config params
$config = sprintf("%sconfig/%s_params.php", BASE_PATH, $app['getEnv']());
$app['params'] = require_once $config;

//Register service providers
$app->register(new TwigServiceProvider(), [
    'twig.path' => $app['params']['twig']['path']
]);

$app->register(new Silex\Provider\MonologServiceProvider(), [
    'monolog.logfile' => __DIR__.'/../runtime/testrocket.log',
    'monolog.level' => $app['params']['log_level']
]);

$app->register(new SessionServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new AssetServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app->register(new SilexGuzzle\GuzzleServiceProvider(),[]);

//Register routes and their handlers
require_once BASE_PATH.'config/routes/bootstrap.php';

//Application instance
return $app;
