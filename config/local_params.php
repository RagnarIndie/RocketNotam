<?php
return [
    'name' => 'Test Rocket',
    'version' => '0.0.1',
    'google_maps' => [
        'api_key' => 'AIzaSyChybv0_RB7CXlqK1mPHFVfbg4vsE1z844',
    ],
    'rocket_route' => [
        'credentials' => [
            'username' => 'ragnar.indie@gmail.com',
            'password' => '68zn5ZbJJLGGQgv58Pnr',
            'device_id' => 'irfUw3djdeDude823ide45',
            'api_key' => 'CHCyTnfQcsaSyL6Em2N2',
        ],
        'api' => [
            'auth_url' => 'https://flydev.rocketroute.com/remote/auth',
            'notam_url' => 'https://apidev.rocketroute.com/notam/v1/service.wsdl'
        ]
    ],
    'console' => [
        'bin_dir' => BASE_PATH.'bin',
        'bin_name' => 'console'
    ],
    'twig' => [
        'path' => [
            BASE_PATH.'templates',
            LIB_PATH.'RocketRoute/Requests',
        ]
    ],
    'log_level' => \Monolog\Logger::DEBUG,
    'debug' => true
];