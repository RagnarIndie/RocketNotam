<?php

namespace App\Helpers;


trait XmlTools
{

    /**
     * @param string $xml
     * @return array
     */
    public function xmlToArray(string $xml): array
    {
        $result = [];

        $xml = simplexml_load_string($xml);
        $result = json_decode(json_encode((array) $xml), true);
        $result = array($xml->getName() => $result);

        return $result;
    }
}