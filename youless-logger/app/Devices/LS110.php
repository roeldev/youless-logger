<?php declare(strict_types=1);

namespace Casa\YouLess\Devices;

use Stellar\Common\StringUtil;
use Stellar\Curl\Curl;
use Stellar\Curl\Request\Request;
use Stellar\Curl\Response\JsonResponse;
use Stellar\Exceptions\Common\MissingArgument;

class LS110 implements DeviceInterface
{
    protected $_name;

    protected $_host;

    protected $_password;

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

    public function __construct(string $name, array $settings)
    {
        $host = $settings['host'] ?? null;
        if (empty($host) || !\is_string($host)) {
            throw MissingArgument::factory(static::class, 'host')->create();
        }

        $this->_name = $name;
        $this->_host = StringUtil::suffix($host, '/');
        $this->_password = $settings['password'] ?? null;
    }

    /** {@inheritDoc} */
    public function getName() : string
    {
        return $this->_name;
    }

    /** {@inheritDoc} */
    public function getHost() : string
    {
        return $this->_host;
    }

    /** {@inheritDoc} */
    public function getIp() : ?string
    {
        $host = $this->getHost();
        if (!$host) {
            return null;
        }

        $result = \gethostbyname($host);
        $result = \rtrim($result, '/');
        $result = \str_replace('http://', '', $result);

        return $result;
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
        return Curl::get($this->getHost() . StringUtil::unprefix($path, '/'));
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
