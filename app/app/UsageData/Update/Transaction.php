<?php declare(strict_types=1);

namespace Casa\YouLess\UsageData\Update;

use Casa\YouLess\Database;
use Casa\YouLess\Device\Device;
use Casa\YouLess\Exceptions\UnknownDelta;
use Casa\YouLess\UsageData\Interval;
use Casa\YouLess\UsageData\IntervalRegistry;
use Casa\YouLess\UsageData\Service;
use Casa\YouLess\UsageData\UnitRegistry;
use Psr\Log\LoggerAwareTrait;

final class Transaction
{
    use LoggerAwareTrait;

    /** @var Database */
    private $_db;

    /** @var Device */
    private $_device;

    /** @var Service */
    private $_service;

    /** @var Interval */
    private $_interval;

    public function __construct(Database $db)
    {
        $this->_db = $db;
    }

    public function fromRequestBuilder(RequestBuilder $builder) : self
    {
        $this->_device = $builder->getDevice();
        $this->_service = $builder->getService();
        $this->_interval = $builder->getInterval();

        return $this;
    }

    public function commit(Response $response) : void
    {
        $unit = UnitRegistry::instance()->get($response->getUnit());
        $values = $response->getValues();

        $this->_db->beginTransaction();

        foreach ($values as $timestamp => $value) {
            $query = $this->_db->prepare('
                INSERT OR IGNORE INTO `data`(`timestamp`, `device_id`, `service_id`, `interval_id`, `value`, `unit_id`)
                VALUES(:timestamp, :device_id, :service_id, :interval_id, :value, :unit_id)'
            );

            $query->execute([
                ':timestamp' => $timestamp,
                ':device_id' => $this->_device->getId(),
                ':service_id' => $this->_service->getId(),
                ':interval_id' => $this->_interval->getId(),
                ':value' => $value,
                ':unit_id' => $unit->getId(),
            ]);
        }

        $this->_db->commit();
    }
}
