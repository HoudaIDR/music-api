<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    $db = require __DIR__ . '/../config/db.php';   // local ou alwaysdata

    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () use ($db) {
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                // Base de données
                'db' => [
                    'host'     => $db['host'],
                    'port'     => $db['port'],
                    'database' => $db['database'],
                    'username' => $db['username'],
                    'password' => $db['password'],
                    'charset'  => 'utf8mb4',
                    'flags'    => [
                        PDO::ATTR_PERSISTENT         => false,                 // pas de connexion persistante
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // erreurs SQL → exceptions
                        PDO::ATTR_EMULATE_PREPARES   => true,                  // émuler les requêtes préparées
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      // tableaux associatifs
                    ],
                ],
            ]);
        }
    ]);
};