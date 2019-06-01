<?php declare(strict_types=1);

namespace Casa\YouLess\Device;

use Casa\YouLess\Exceptions\EmptyEnv;
use Stellar\Curl\Curl;
use Stellar\Curl\Request\Request;
use Stellar\Curl\Response\JsonResponse;

class LS110 implements DeviceInterface
{
    protected $_name;

    protected $_mac;

    /** {@inheritDoc} */
    public static function getPowerRange() : array
    {
        return [
            'h' => 2,
            'w' => 3,
            'd' => 7,
            'm' => 12,
        ];
    }

    /** {@inheritDoc} */
    public static function getGasRange() : array
    {
        return [];
    }

    /** {@inheritDoc} */
    public static function getS0Range() : array
    {
        return [];
    }

    public function __construct(string $name)
    {
        $this->_name = $name;
    }

    /** {@inheritDoc} */
    public function getName() : string
    {
        return $this->_name;
    }

    /** {@inheritDoc} */
    public function getHost() : ?string
    {
        $host = \getenv('YOULESS_HOST');
        if (!$host || !\is_string($host)) {
            return null;
        }

        return \rtrim($host, '/');
    }

    /** {@inheritDoc} */
    public function getIp() : ?string
    {
        $host = $this->getHost();
        if (!$host) {
            return null;
        }

        return \str_replace('http://', '', \gethostbyname($host));
    }

    /** {@inheritDoc} */
    public function getModel() : string
    {
        return 'LS110';
    }

    /** {@inheritDoc} */
    public function getMac() : ?string
    {
        if (!$this->_mac) {
            $response = $this->createRequest('/d')
                ->response(JsonResponse::class)
                ->toArray();

            $this->_mac = $response['mac'] ?? null;
        }

        return $this->_mac;
    }

    /** {@inheritDoc} */
    public function createRequest(string $path) : Request
    {
        $host = $this->getHost();
        if (!$host) {
            throw EmptyEnv::factory('YOULESS_HOST')->create();
        }

        return Curl::get($host . $path);
    }

    public function toArray() : array
    {
        return [
            'name' => $this->getName(),
            'model' => $this->getModel(),
            'ip' => $this->getIp(),
            'mac' => $this->getMac(),
        ];
    }
}
