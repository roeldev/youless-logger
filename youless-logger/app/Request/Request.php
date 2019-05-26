<?php declare(strict_types=1);

namespace Casa\YouLess\Request;

use Casa\YouLess\Device;
use Casa\YouLess\Exceptions\EmptyEnv;
use Casa\YouLess\Response\DeviceInfo;
use Casa\YouLess\Response\UsageData;
use Stellar\Common\StaticClass;
use Stellar\Curl\Curl;
use Stellar\Curl\Request\Request as CurlRequest;
use Stellar\Curl\Response\JsonResponse;

class Request extends StaticClass
{
    protected function _createCurlRequest(string $uri) : CurlRequest
    {
        $host = Device::getHost();
        if (!$host) {
            throw EmptyEnv::factory('YOULESS_HOST')->create();
        }

        return Curl::get($host . $uri)
            ->withQueryParam('f', 'j')
            ->withResponseAs(JsonResponse::class);
    }

    public static function deviceInfo() : CurlRequest
    {
        return self::_createCurlRequest('/d')
            ->withResponseAs(DeviceInfo::class);
    }

    public static function updatePower() : CurlRequest
    {
        // h = per minute of day
        // w = per 10 mins.
        // d = per hour
        // m = per day
        return self::_createCurlRequest('/V')
            ->withQueryParam('h', '1')
            ->withResponseAs(UsageData::class);
    }
}
