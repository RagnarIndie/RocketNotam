<?php

namespace App\Extensions;

use Symfony\Component\HttpFoundation\Response;


class JsonController
{
    protected function sendJson(array $data, int $httpCode = 200): Response
    {
        return new Response(
            json_encode($data),
            $httpCode,
            ['Content-Type' => 'application/json']
        );
    }
}