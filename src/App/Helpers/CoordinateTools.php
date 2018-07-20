<?php

namespace App\Helpers;


trait CoordinateTools
{

    /**
     * @param int $degrees
     * @param int $minutes
     * @param string $orientation
     * @param int $seconds
     * @return float
     */
    public function calcWorldCoordinates(int $degrees, int $minutes, string $orientation, int $seconds = 0): float
    {
        $result = 0.0;

        $calcCoord = $degrees + ($minutes / 60);

        if ($seconds > 0) {
            $calcCoord += $seconds / 3600;
        }

        if ($orientation == 'S' || $orientation == 'W') {
            $calcCoord *= -1;
        }

        $result = number_format($calcCoord, 5);

        return $result;
    }
}