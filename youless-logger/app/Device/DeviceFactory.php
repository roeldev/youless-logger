<?php declare(strict_types=1);

namespace Casa\YouLess\Device;

use Casa\YouLess\Database;
use Casa\YouLess\Device\Models\LS120;
use Casa\YouLess\Exceptions\UnknownDevice;
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

    protected function _queryRecord(string $statement, ...$args)
    {
        $query = Database::instance()->prepare($statement);
        $query->execute($args);

        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    public function __construct()
    {
        $this->_container = Registry::container(self::class);
    }

    public function create(string $name, array $config) : Device
    {
        return $this->_container->request($name, function () use ($name, $config) {
            $record = $this->_queryRecord('SELECT * FROM `devices` WHERE `ip` = ?', $config['ip']) ?:
                      $this->_queryRecord('SELECT * FROM `devices` WHERE `name` = ?', $name) ?:
                      [];

            $device = new Device(new LS120(), $name, $config, $record);
            if ($device->isDirty()) {
                $device->save();
            }

            return new ServiceRequest($device);
        });
    }

    /**
     * @throws UnknownDevice
     */
    public function get(string $name = 'default') : Device
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
