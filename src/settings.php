<?php
return [
    'settings' => [
        'determineRouteBeforeAppMiddleware' => true,
        'displayErrorDetails' => true, // set to false in production

        // Template renderer settings
        'view' => [
            'template_path' => __DIR__ . '/../templates/',
            'cache_path' => false, // Set to 'cache' in production
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],

        // PDO configuration
        'db' => [
            'dsn'   => 'mysql:dbname=asset-library;host=127.0.0.1',
            'user'  => 'user', // Check settings-local.php for those two
            'pass'  => 'pass',
        ],

        // Mail configuration
        'mail' => [
            'from' => 'no-reply@localhost.localdomain',
            // 'replyTo' => '',
            // 'smtp' => [
            //   'host' => '',
            //   'port' => 0,
            //   'auth' => ['user' => 'user', 'pass' => 'pass'],
            //   'secure' => ''
            // ],
        ],

        // Auth configuration
        'auth' => [
            'secret' => 'somerandomstringshouldbeputhere', // Check settings-local.php
            'tokenExpirationTime' => 3600 * 24 * 7, // week
            'tokenSessionBytesLength' => 24, // If set to something which isn't 24, change DB schema
            'tokenResetBytesLength' => 32, // If set to something which isn't 32, change DB schema
            'bcryptOptions' => [
              'cost' => 12,
            ],
        ],
    ],
];
