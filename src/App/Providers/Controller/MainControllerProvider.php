<?php

namespace App\Providers\Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use App\Controllers\Main;


class MainControllerProvider implements ControllerProviderInterface
{
    protected $instance = null;

    public function connect(Application $app)
    {
        if ($this->instance == null) {
            $this->instance = new Main();
        }

        $controllers = $app['controllers_factory'];

        //GET
        $controllers->get("/", [$this->instance, 'index']);

        return $controllers;
    }
}