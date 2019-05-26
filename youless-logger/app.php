#!/usr/bin/env php
<?php declare(strict_types=1);

$autoloader = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloader)) {
    exit(1);
}

require_once __DIR__ . '/vendor/autoload.php';

use Casa\YouLess\Commands\Device\DeviceCommand;
use Casa\YouLess\Commands\Device\DeviceIpCommand;
use Casa\YouLess\Commands\Device\DeviceMacCommand;
use Casa\YouLess\Commands\Device\DeviceModelCommand;
use Casa\YouLess\Commands\Update\UpdateCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

$app = new Application('Logger service for YouLess energy monitor');
$app->addCommands([
    new DeviceCommand(),
    new DeviceIpCommand(),
    new DeviceMacCommand(),
    new DeviceModelCommand(),
    new UpdateCommand(),
]);
$app->run();
