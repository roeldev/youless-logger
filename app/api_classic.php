<?php declare(strict_types=1);

use Casa\YouLess\Api\Classic\GetDataAction;
use Casa\YouLess\Api\Classic\GetDeviceInfoAction;

$autoloader = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloader)) {
    exit(1);
}

require_once __DIR__ . '/vendor/autoload.php';

$app = new Casa\YouLess\Api\App();
$app->addActions(
    GetDataAction::class,
    GetDeviceInfoAction::class
);
$app->run();
