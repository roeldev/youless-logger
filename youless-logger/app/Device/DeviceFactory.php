<?php declare(strict_types=1);

namespace Casa\YouLess\Device;

use Stellar\Container\AbstractFactory;
use Stellar\Container\Container;
use Stellar\Container\Registry;
use Stellar\Container\ServiceRequest;

final class DeviceFactory extends AbstractFactory
{
    /** @var Container */
    protected $_container;

    public function __construct()
    {
        $this->_container = Registry::container(self::class);
    }

    public function create(string $name, array $settings) : DeviceInterface
    {
        return $this->_container->request($name, function () use ($name, $settings) {
            return new ServiceRequest(new LS120($name, $settings));
        });
    }

    public function get(string $name = 'default') : DeviceInterface
    {
        return $this->_container->get($name);
    }
}
