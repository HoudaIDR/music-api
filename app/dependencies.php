<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Doctrine\ORM\EntityManager;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },

        // Connexion à la base de données (paramètres lus dans app/settings.php)
        PDO::class => function (ContainerInterface $c) {
            $db = $c->get(SettingsInterface::class)->get('db');
            $dsn = "mysql:host={$db['host']};port={$db['port']};"
                . "dbname={$db['database']};charset={$db['charset']}";
            return new PDO($dsn, $db['username'], $db['password'], $db['flags']);
        },

        // Doctrine : l'EntityManager dans le conteneur
        EntityManager::class => function () {
            return require __DIR__ . '/../config/doctrine.php';
        },
    ]);
};