<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Secrets\DbConn;

require_once __DIR__.'/vendor/autoload.php';

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$proxyDir = __DIR__.'/Proxies';
$cache = null;
$useSimpleAnnotationReader = false;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__.'/src/Entity'), $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);

// Obtain the entity manager
$entityManager = EntityManager::create(DbConn::PARAMS, $config);
