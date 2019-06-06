<?php declare(strict_types=1);

namespace Casa\YouLess\Devices;

use Casa\YouLess\Exceptions\UnknownDevice;
use Stellar\Container\AbstractFactory;
use Stellar\Container\Container;
use Stellar\Container\Exceptions\NotFound;
use Stellar\Container\Registry;
use Stellar\Container\ServiceRequest;
use Stellar\Container\Traits\SingletonInstanceTrait;

final class DeviceFactory
{
    use SingletonInstanceTrait;

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

    /**
     * @throws UnknownDevice
     */
    public function get(string $name = 'default') : DeviceInterface
    {
        try {
            return $this->_container->get($name);
        }
        catch (NotFound $notFound) {
            throw UnknownDevice::factory($name)
                ->withPrevious($notFound)
                ->create();
        }
    }
}
