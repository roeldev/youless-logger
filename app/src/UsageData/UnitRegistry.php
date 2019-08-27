<?php declare(strict_types=1);

namespace Casa\YouLess\UsageData;

use Casa\YouLess\Database\Database;
use Casa\YouLess\Exceptions\InvalidUnit;
use Stellar\Container\ServiceRequest;

final class UnitRegistry extends AbstractRegistry
{
    public function get(string $name) : Unit
    {
        $name = \strtolower($name);

        return $this->_container->request($name, [ $this, 'requestService' ], [ $name ]);
    }

    public function getRecord(string $name) : ?array
    {
        $query = Database::instance()
            ->query('SELECT * FROM `units` WHERE `name` = ? OR `alias` = ?', \PDO::FETCH_ASSOC);

        $query->execute([ $name, $name ]);
        $result = $query->fetch();

        return \is_array($result) ? $result : null;
    }

    public function requestService(string $name) : ServiceRequest
    {
        $record = $this->getRecord($name);
        if (empty($record)) {
            throw new InvalidUnit($name);
        }

        $unit = new Unit($record);
        $serviceRequest = ServiceRequest::with($unit)->asSingleton();

        if ($unit->getName() !== $unit->getAlias()) {
            $serviceRequest->withAlias($unit->getName(), 'name');
            $serviceRequest->withAlias($unit->getAlias(), 'alias');
        }

        return $serviceRequest;
    }
}
