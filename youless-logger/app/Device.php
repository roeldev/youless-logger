<?php declare(strict_types=1);

namespace Casa\YouLess;

use Stellar\Common\StaticClass;

class Device extends StaticClass
{
    public static function getIp() : string
    {
        return str_replace('http://', '', \gethostbyname(\getenv('YOULESS_HOST')));
    }
}
