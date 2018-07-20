<?php

namespace App\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Extensions\JsonController;
use App\Library\RocketRoute\Manager as RocketManager;

class Ajax extends JsonController
{

    public function beforeAction(Request $request, Application $app)
    {
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false;

        if (!$isAjax) {
            return new Response('Page not found', 404);
        }
    }

    public function notamLookup(Request $request, Application $app)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $response = [
            'success' => false,
            'error' => false
        ];

        if ($icaoCode = $request->request->get('icao')) {
            $rocketManager = new RocketManager($app);

            if ($notamData = $rocketManager->getNotamData($icaoCode)) {
                $response['notamData'] = $rocketManager->jsonSerialize($notamData);
                $response['success'] = true;
            } else {
                $response['error'] = 'Can not get NOTAM data';
            }
        } else {
            $respose['error'] = 'Wrong or missing ICAO code';
        }

        return $this->sendJson($response);
    }
}
