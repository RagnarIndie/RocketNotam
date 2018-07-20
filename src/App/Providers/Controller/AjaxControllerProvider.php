<?php

namespace App\Providers\Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use App\Controllers\Ajax;


class AjaxControllerProvider implements ControllerProviderInterface
{
    protected $instance = null;

    public function connect(Application $app)
    {
        if ($this->instance == null) {
            $this->instance = new Ajax();
        }

        $controllers = $app['controllers_factory'];

        $controllers
            ->post("/notam-lookup", [$this->instance, 'notamLookup'])
            ->before([$this->instance, 'beforeAction']);
        return $controllers;
    }
}