<?php declare(strict_types=1);

namespace Casa\YouLess\Request;

use Casa\YouLess\Exceptions\EmptyEnv;
use Casa\YouLess\Response\DeviceInfo;
use Stellar\Common\StaticClass;
use Stellar\Curl\Curl;
use Stellar\Curl\Request\Request as CurlRequest;

class Request extends StaticClass
{
    public static function deviceInfo() : CurlRequest
    {
        $host = \getenv('YOULESS_HOST');
        if (!$host) {
            throw EmptyEnv::factory('YOULESS_HOST')->create();
        }

        return Curl::get($host . '/d')
            ->withResponseAs(DeviceInfo::class);
    }
}
