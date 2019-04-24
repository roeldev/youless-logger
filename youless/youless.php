#!/usr/bin/env php
<?php declare(strict_types=1);

$autoloader = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloader)) {
    exit(1);
}

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;

$app = new Application();
$app->add(new Casa\YouLess\Commands\FetchCommand());
$app->run();
