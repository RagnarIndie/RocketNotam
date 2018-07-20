<?php

namespace App\Helpers;


trait ArrayTools
{

    /**
     * @param array $arr
     * @return bool
     */
    public function isAssoc(array $arr): bool
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

}