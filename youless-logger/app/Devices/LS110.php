<?php declare(strict_types=1);

namespace Casa\YouLess\Devices;

use Stellar\Common\StringUtil;
use Stellar\Curl\Curl;
use Stellar\Curl\Request\Request;
use Stellar\Curl\Response\JsonResponse;
use Stellar\Exceptions\Common\MissingArgument;

class LS110 implements DeviceInterface
{
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

    /** @var string */
    protected $_name;

    /** @var string */
    protected $_host;

    /** @var string */
    protected $_ip;

    /** @var string */
    protected $_password;

    /** @var string */
    protected $_mac;

    /** @var string[] */
    protected $_activeTypes = [];

    public function __construct(string $name, array $settings)
    {
        $ip = $settings['ip'] ?? null;
        if (empty($ip) || !\is_string($ip)) {
            throw MissingArgument::factory(static::class, 'ip')->create();
        }

        $this->_name = $name;
        $this->_host = \sprintf('http://%s/', $ip);
        $this->_ip = $ip;
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
        return $this->_ip;
    }

    /** {@inheritDoc} */
    public function getIp() : ?string
    {
        return $this->_ip;
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
            'host' => $this->getHost(),
            'ip' => $this->getIp(),
            'mac' => $this->getMac(),
        ];
    }
}
