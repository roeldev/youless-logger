<?php declare(strict_types=1);

namespace Casa\YouLess\Device;

use Casa\YouLess\Device\Models\ModelInterface;
use Stellar\Common\StringUtil;
use Stellar\Curl\Curl;
use Stellar\Curl\Request\Request;
use Stellar\Curl\Response\JsonResponse;
use Stellar\Exceptions\Common\MissingArgument;

class Device
{
    /** @var int */
    protected $_id;

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

    /** @var ModelInterface */
    protected $_model;

    /** @var array<string, array<string, int>> */
    protected $_services = [];

    protected function _determineActiveServices(array $services) : array
    {
        $result = [];

        $validServices = $this->_model->getServices();
        foreach ($validServices as $service) {
            if (true === ($services[ $service ] ?? false) || \in_array($service, $services, true)) {
                $result[] = $service;
            }
        }

        return $result;
    }

    public function __construct(ModelInterface $model, string $name, array $config, array $record = [])
    {
        $ip = $config['ip'] ?? null;
        if (empty($ip) || !\is_string($ip)) {
            throw MissingArgument::factory(static::class, 'ip')->create();
        }

        $this->_id = $record['id'] ?? null;
        $this->_name = $name;
        $this->_host = \sprintf('http://%s/', $ip);
        $this->_ip = $ip;
        $this->_password = $config['password'] ?? null;

        $this->_model = $model;
        if (!isset($config['services'])) {
            $this->_services = $model->getServices();
        }
        else {
            $this->_services = $this->_determineActiveServices($config['services']);
        }
    }

    public function getId() : ?int
    {
        return $this->_id;
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
        return $this->_ip;
    }

    /** {@inheritDoc} */
    public function getModel() : ModelInterface
    {
        return $this->_model;
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
    public function getActiveServices() : array
    {
        return $this->_services;
    }

    /** {@inheritDoc} */
    public function createRequest(string $path) : Request
    {
        return Curl::get($this->getHost() . StringUtil::unprefix($path, '/'))
            ->throwExceptionOnFailure();
    }

    public function toArray() : array
    {
        return [
            'name' => $this->getName(),
            'model' => (string) $this->getModel(),
            'host' => $this->getHost(),
            'ip' => $this->getIp(),
            'mac' => $this->getMac(),
        ];
    }
}
