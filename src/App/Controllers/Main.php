<?php

namespace App\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Main
{

    public function index(Request $request, Application $app)
    {
        $icaoCodePattern = '^[a-zA-Z]{4}$';

        return $app['twig']->render(
            'main/index.html.twig', 
            [
                'icaoCodePattern' => $icaoCodePattern,
                'error' => ''
            ]
        );
    }
}
