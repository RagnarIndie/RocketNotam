<?php

namespace App\Library\RocketRoute;

use Pimple\Container;
use Symfony\Component\Config\Definition\Exception\Exception;
use App\Helpers\XmlTools;

class ApiClient 
{

    //Use xml helper trait
    use XmlTools;

    //Successful notam request
    const NOTAM_RESULT_SUCCESS = 0;

    /**
     * @var \Pimple\Container
     */
    protected $app = null;

    /**
     * @var string
     */
    protected $apiPassword = null;

    /**
     * @var \Pimple\Container
     */
    public function __construct(Container $app)
    {
        if ($app != null) {
            $this->app = $app;
        }
    }

    /**
     * @return \App\Library\RocketRoute\ApiClient
     */
    public function auth(): ApiClient
    {
        if ($this->app != null) {
            $urls = $this->getApiUrls();
            $credentials = $this->getAuthCredentials();

            //Prepare password and deviceId hashes
            $credentials['password'] = md5($credentials['password']);

            //Render xml with an auth credentials
            $requestBody = $this->app['twig']->render('auth.xml.twig', ['auth' => $credentials]);

            if ($requestBody && !empty($urls['auth_url']) && !empty($credentials)) {
                try {
                    $response = $this->app['guzzle']
                        ->request(
                            'POST',
                            $urls['auth_url'],
                            [
                                'form_params' => [
                                    'req' => $requestBody
                                ]
                            ]
                        );

                    if ($respXml = (string) $response->getBody()) {
                        $respArray = $this->xmlToArray($respXml);

                        if (!empty($respArray['AUTH']['RESULT']) && $respArray['AUTH']['RESULT'] == 'SUCCESS') {
                            $this->apiPassword = $respArray['AUTH']['KEY'];
                        }
                    }
                } catch (Exception $exception) {
                    $this->app['monolog']->error($exception->getMessage());
                }
            }
        }

        return $this;
    }

    /**
     * @param string $icaoCode
     * @return array
     */
    public function getRawNotamData(string $icaoCode): array
    {
        $result = [];

        if (!empty($icaoCode) && ($apiUrls = $this->getApiUrls())) {
            if ($this->apiPassword == null) {
                $this->auth();
            }

            if (!empty($apiUrls['notam_url'])) {
                $credentials = $this->getAuthCredentials();

                $requestBody = $this->app['twig']->render('getnotam.xml.twig', [
                    'username' => $credentials['username'],
                    'password' => md5($credentials['password']),
                    'icaoCode' => strtoupper($icaoCode)
                ]);

                $soapClient = $this->getSoapClient($apiUrls['notam_url']);

                $notamRawArray = $this->xmlToArray(
                    $soapClient->getNotam($requestBody)
                );

                if (!empty($notamRawArray['REQNOTAM'])) {
                    $notamResult = (int) $notamRawArray['REQNOTAM']['RESULT'];

                    if ($notamResult == self::NOTAM_RESULT_SUCCESS) {
                        $result = $notamRawArray['REQNOTAM']['NOTAMSET'] ?? [];
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param string $url
     * @return \SoapClient
     */
    protected function getSoapClient(string $url): \SoapClient
    {
        return new \SoapClient(
            $url,
            [
                'trace' => $this->app['params']['debug']
            ]
        );
    }

    /**
     * @return array
     */
    protected function getAuthCredentials(): array
    {
        $result = [];

        if (!empty($this->app['params']['rocket_route'])) {
            $result = $this->app['params']['rocket_route']['credentials'];
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getApiUrls(): array
    {
        $result = [];

        if (!empty($this->app['params']['rocket_route'])) {
            $result = $this->app['params']['rocket_route']['api'];
        }

        return $result;
    }
}