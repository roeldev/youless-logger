<?php declare(strict_types=1);

namespace Casa\YouLess\Device;

use Casa\YouLess\Database\QueryRecordTrait;
use Casa\YouLess\Device\Models\LS120;
use Casa\YouLess\Device\Models\Model;
use Casa\YouLess\Exceptions\UnknownDevice;
use Stellar\Common\Contracts\SingletonInterface;
use Stellar\Container\Abilities\SingletonInstanceTrait;
use Stellar\Container\Exceptions\NotFoundException;
use Stellar\Container\Container;
use Stellar\Container\Registry;
use Stellar\Container\ServiceRequest;

final class DeviceFactory implements SingletonInterface
{
    use QueryRecordTrait;
    use SingletonInstanceTrait;

    /** @var Container */
    protected $_container;

    /** @var bool */
    protected $_initialized = false;

    protected function _createModel() : Model
    {
        return new LS120();
    }

    public function __construct()
    {
        $this->_container = Registry::container(self::class);
    }

    public function init(array $devices) : void
    {
        if ($this->_initialized) {
            return;
        }

        $this->_initialized = true;
        foreach ($devices as $name => $config) {
            $this->_container->request($name, [ $this, 'requestService' ], [ $name, $config ]);
        }
    }

    /**
     * @throws UnknownDevice
     */
    public function get(string $name = 'default') : Device
    {
        try {
            return $this->_container->get($name);
        }
        catch (NotFoundException $notFound) {
            throw new UnknownDevice($name, $notFound);
        }
    }

    public function requestService(string $name, array $config) : ServiceRequest
    {
        $record = $this->queryRecord('SELECT * FROM `devices` WHERE `ip` = ? OR `name` = ?', $config['ip'], $name);
        $model = $this->_createModel();
        $device = new Device($model, $name, $config, $record);

        if ($device->isDirty()) {
            $device->save();
        }

        return ServiceRequest::with($device)
            ->asSingleton()
            ->withAlias((string) $device->getId());
    }
}
