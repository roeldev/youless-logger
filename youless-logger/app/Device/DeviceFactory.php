<?php declare(strict_types=1);

namespace Casa\YouLess\Device;

use Casa\YouLess\Database\QueryRecordTrait;
use Casa\YouLess\Device\Models\LS120;
use Casa\YouLess\Exceptions\UnknownDevice;
use Stellar\Common\Contracts\SingletonInterface;
use Stellar\Container\Container;
use Stellar\Container\Exceptions\NotFound;
use Stellar\Container\Registry;
use Stellar\Container\ServiceRequest;
use Stellar\Container\Traits\SingletonInstanceTrait;

final class DeviceFactory implements SingletonInterface
{
    use QueryRecordTrait;
    use SingletonInstanceTrait;

    /** @var Container */
    protected $_container;

    protected function _requestService(string $name, array $config) : ServiceRequest
    {
        $record = $this->queryRecord('SELECT * FROM `devices` WHERE `ip` = ? OR `name` = ?', $config['ip'], $name);
        $device = new Device(new LS120(), $name, $config, $record);

        if ($device->isDirty()) {
            $device->save();
        }

        return ServiceRequest::with($device)
            ->asSingleton()
            ->withAlias((string) $device->getId());
    }

    public function __construct()
    {
        $this->_container = Registry::container(self::class);
    }

    public function init(array $devices) : void
    {
        $requestServiceFn = \Closure::fromCallable([ $this, '_requestService' ]);
        foreach ($devices as $name => $config) {
            $this->_container->request($name, $requestServiceFn, $name, $config);
        }
    }

    /**
     * @throws UnknownDevice
     */
    public function getFromName(string $name = 'default') : Device
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
