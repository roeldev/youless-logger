<?php declare(strict_types=1);

namespace Casa\YouLess;

use Stellar\Common\StaticClass;

class Device extends StaticClass
{
    public static function getHost() : ?string
    {
        $host = \getenv('YOULESS_HOST');
        if (!$host || !\is_string($host)) {
            return null;
        }

        return rtrim($host, '/');
    }

    public static function getIp() : ?string
    {
        $host = self::getHost();
        if (!$host) {
            return null;
        }

        return str_replace('http://', '', \gethostbyname($host));
    }
}
