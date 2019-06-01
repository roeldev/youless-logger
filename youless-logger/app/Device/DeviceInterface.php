<?php declare(strict_types=1);

namespace Casa\YouLess\Device;

use Stellar\Common\Contracts\ArrayableInterface;
use Stellar\Curl\Request\Request;

interface DeviceInterface extends ArrayableInterface
{
    public static function getPowerRange() : array;

    public static function getGasRange() : array;

    public static function getS0Range() : array;

    public function getName() : string;

    public function getHost() : ?string;

    public function getIp() : ?string;

    public function getModel() : string;

    public function getMac() : ?string;

    public function createRequest(string $path) : Request;
}
