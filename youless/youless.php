<?php declare(strict_types=1);

$autoloader = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloader)) {
    require_once __DIR__ . '/vendor/autoload.php';
}

while (true) {
    sleep(1);
    echo time() . PHP_EOL;
}
