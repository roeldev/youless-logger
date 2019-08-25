<?php declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('error_reporting', (string) E_ALL);

$autoloader = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloader)) {
    exit(1);
}

require_once __DIR__ . '/vendor/autoload.php';

$app = new Casa\YouLess\Api\App();
$app->addController(new Casa\YouLess\Api\ClassicApiController());
$app->run();
