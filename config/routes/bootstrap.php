<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//Mount controller providers for route namespaces
$app->mount('/', new \App\Providers\Controller\MainControllerProvider());
$app->mount('/ajax', new \App\Providers\Controller\AjaxControllerProvider());

if ($app['getEnv']() == 'local') {
    $app->get('/phpinfo', function (Request $request, \Silex\Application $app) {
        phpinfo();
        return new Response();
    });
}

//Errors handler
$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html.twig',
        'errors/'.substr($code, 0, 2).'x.html.twig',
        'errors/'.substr($code, 0, 1).'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});