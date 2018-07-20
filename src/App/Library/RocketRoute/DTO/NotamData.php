<?php

namespace App\Library\RocketRoute\DTO;

use App\Helpers\CoordinateTools;
use App\Extensions\ISerializable;


class NotamData implements ISerializable
{

    use CoordinateTools;

    const NAUTICAL_MILE_IN_KM = 1852;

    /**
     * @var string
     */
    protected $id = "";

    /**
     * @var string
     */
    protected $icao = "";

    /**
     * @var float
     */
    protected $latitude = 0.0;

    /**
     * @var float
     */
    protected $longitude = 0.0;

    /**
     * @var int
     */
    protected $radius = 0;

    /**
     * @var array
     */
    protected $notam = [];

    /**
     * @var string
     */
    protected $rendered = "";

    /**
     * @var array
     */
    protected $rawData = [];

    /**
     * @var string
     *
     * 1 Group - whole coord string
     * 2 Group - lat
     * 3 Group - long
     * 4 Group - radius
     */
    protected $coordinatesPattern = '/((\d{4}[N|S])(\d{5}[E|W])(\d{3})?)/';

    /**
     * @var string
     */
    protected $notamItemPattern = '/Item([A|B|D|E|F|G|Q])/';

    /**
     * @var string
     */
    protected $notamLatPattern = '/(\d{2})(\d{2})([N|S])/';

    /**
     * @var string
     */
    protected $notamLongPattern = '/(\d{3})(\d{2})([E|W])/';

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIcao(): string
    {
        return $this->icao;
    }

    public function getRendered(): string
    {
        return $this->rendered;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @return int
     */
    public function getRadius(): int
    {
        return $this->radius;
    }

    /**
     * @return array
     */
    public function getNotam(): array
    {
        return $this->notam;
    }

    /**
     * @param string $value
     */
    public function setIcao(string $value)
    {
        if (!empty($value) && strlen($value) == 4) {
            $this->icao = $value;
        }
    }

    /**
     * @param string $value
     */
    public function setRendered(string $value)
    {
        if (!empty($value)) {
            $this->rendered = $value;
        }
    }

    /**
     * @param array $value
     */
    public function setData(array $value)
    {
        if (!empty($value)) {
            $this->rawData = $value;
            $this->formatData();
        }
    }

    /**
     * Parses NOTAM data to DTO object
     */
    protected function formatData()
    {
        if (!empty($this->rawData['@attributes']['id'])) {
            $this->id = $this->rawData['@attributes']['id'];

            unset($this->rawData['@attributes']);
        }

        if (count($this->rawData)) {
            foreach ($this->rawData as $item => $value) {
                $itemMatches = null;

                if (preg_match($this->notamItemPattern, $item, $itemMatches)) {
                    if (!empty($itemMatches[1])) {
                        if (!empty($value) && !is_array($value)) {
                            array_push(
                                $this->notam,
                                sprintf("%s) %s", $itemMatches[1], (string) $value)
                            );
                        }

                        if ($itemMatches[1] == 'Q') {
                            $coordMatches = null;

                            if (preg_match($this->coordinatesPattern, $value, $coordMatches)) {
                                $this->parseCoordinates($coordMatches);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param array $coords
     */
    protected function parseCoordinates(array $coords)
    {
        if (count($coords)) {
            $latNotam = $coords[2] ?? false;
            $longNotam = $coords[3] ?? false;
            $radius = $coords[4] ?? false;

            //Calc latitude
            if ($latNotam) {
                $latMatches = null;
                preg_match($this->notamLatPattern, $latNotam, $latMatches);

                if (count($latMatches) == 4) {
                    $degrees = (int) $latMatches[1];
                    $minutes = (int) $latMatches[2];
                    $orientation = $latMatches[3];

                    $this->latitude = $this->calcWorldCoordinates($degrees, $minutes, $orientation);
                }
            }

            //Calc longitude
            if ($longNotam) {
                $longMatches = null;
                preg_match($this->notamLongPattern, $longNotam, $longMatches);

                if (count($longMatches) == 4) {
                    $degrees = (int) $longMatches[1];
                    $minutes = (int) $longMatches[2];
                    $orientation = $longMatches[3];

                    $this->longitude = $this->calcWorldCoordinates($degrees, $minutes, $orientation);
                }
            }

            //Calc radius in km
            if ($radius) {
                $this->radius = (int) $radius * self::NAUTICAL_MILE_IN_KM;
            }
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'ICAO' => $this->getIcao(),
            'lat' => $this->getLatitude(),
            'lon' => $this->getLongitude(),
            'radius' => $this->getRadius(),
            'NOTAM' => $this->getNotam(),
            'rendered' => $this->getRendered()
        ];
    }
}
































