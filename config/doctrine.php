<?php
// config/doctrine.php
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

require_once __DIR__ . '/../vendor/autoload.php';

$db = require __DIR__ . '/db.php';            // local ou alwaysdata
$paths = [__DIR__ . '/../src/Entity'];        // dossier des entités
$isDevMode = true;

$dbParams = [
    'driver'   => 'pdo_mysql',
    'user'     => $db['username'],
    'password' => $db['password'],
    'dbname'   => $db['database'],
    'host'     => $db['host'],
    'port'     => $db['port'],
    'charset'  => 'utf8mb4',
];

// Doctrine 3 : attributs PHP 8 (les annotations sont supprimées)
$config = ORMSetup::createAttributeMetadataConfig($paths, $isDevMode);
if (PHP_VERSION_ID >= 80400) {
    $config->enableNativeLazyObjects(true);   // uniquement si PHP >= 8.4
} else {
    $config->setProxyDir(__DIR__ . '/../var/doctrine/proxies');
    $config->setProxyNamespace('DoctrineProxies');
    $config->setAutoGenerateProxyClasses(true);
}

$connection = DriverManager::getConnection($dbParams, $config);

// Doctrine 3 : EntityManager::create() n'existe plus
return new EntityManager($connection, $config);