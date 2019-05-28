#!/usr/bin/env php
<?php declare(strict_types=1);

$autoloader = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloader)) {
    exit(1);
}

require_once __DIR__ . '/vendor/autoload.php';

use Casa\YouLess\App;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

App::instance()->run();
