#!/usr/bin/env php
<?php declare(strict_types=1);

$autoloader = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloader)) {
    exit(1);
}

require_once __DIR__ . '/vendor/autoload.php';

use Casa\YouLess\Commands\DeviceCommand;
use Casa\YouLess\Commands\DeviceMacCommand;
use Casa\YouLess\Commands\DeviceModelCommand;
use Casa\YouLess\Commands\UpdateCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

$app = new Application();
$app->addCommands([
    new DeviceCommand(),
    new DeviceMacCommand(),
    new DeviceModelCommand(),
    new UpdateCommand(),
]);
$app->run();
