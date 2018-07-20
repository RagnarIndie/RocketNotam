<?php

namespace App\Library\RocketRoute;

use App\Extensions\ISerializable;
use App\Helpers\ArrayTools;
use App\Library\RocketRoute\DTO\NotamData;
use Pimple\Container;

class Manager
{

    use ArrayTools;

    /**
     * @var \App\Library\RocketRoute\ApiClient
     */
    protected $api = null;

    /**
     * @var \Pimple\Container
     */
    protected $app = null;

    /**
     * Manager constructor.
     * @param \Pimple\Container $app
     */
    public function __construct(Container $app)
    {
        if ($app != null) {
            $this->app = $app;
            $this->api = new ApiClient($app);
        }
    }

    /**
     * @param string $icaoCode
     * @return array
     */
    public function getNotamData(string $icaoCode): array
    {
        $result = [];

        if ($this->api && !empty($icaoCode)) {
            $rawData = $this->api
                ->auth()
                ->getRawNotamData(strtoupper($icaoCode));

            if (count($rawData['NOTAM'])) {
                if ($this->isAssoc($rawData['NOTAM'])) {
                    $notamObj = $this->buildNotamDto($icaoCode, $rawData['NOTAM']);
                    array_push($result, $notamObj);
                } else {
                    foreach ($rawData['NOTAM'] as $notamData) {
                        $notamObj = $this->buildNotamDto($icaoCode, $notamData);
                        array_push($result, $notamObj);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param array $data
     * @return string
     */
    public function jsonSerialize(array $data): string
    {
        $result = [];

        if (count($data)) {
            foreach ($data as $item) {
                if ($item instanceof ISerializable) {
                    array_push($result, $item->toArray());
                }
            }
        }

        return json_encode($result);
    }

    /**
     * @param string $icaoCode
     * @param array $notamData
     * @return NotamData
     */
    protected function buildNotamDto(string $icaoCode, array $notamData): NotamData
    {
        $tmpNotamObj = new NotamData();
        $tmpNotamObj->setIcao($icaoCode);
        $tmpNotamObj->setData($notamData);

        //Render Info Window for gmap marker popup
        if ($renderedWindow = $this->renderNotamInfo($tmpNotamObj)) {
            $tmpNotamObj->setRendered($renderedWindow);
        }

        return $tmpNotamObj;
    }

    /**
     * @param NotamData $notamData
     * @return string
     */
    protected function renderNotamInfo(NotamData $notamData): string
    {
        return $this->app['twig']
            ->render(
                'partials/notamInfoWindow.html.twig',
                [
                    'id' => $notamData->getId(),
                    'notam' => $notamData->getNotam()
                ]
            );
    }
}