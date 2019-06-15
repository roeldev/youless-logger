<?php declare(strict_types=1);

namespace Casa\YouLess\UsageData;

use Casa\YouLess\Database;
use Casa\YouLess\Exceptions\InvalidInterval;
use Stellar\Container\ServiceRequest;

final class IntervalRegistry extends AbstractRegistry
{
    public function get(string $name) : Interval
    {
        return $this->_container->request($name, [ $this, 'requestService' ], [ $name ]);
    }

    public function getRecord(string $name) : ?array
    {
        $query = Database::instance()
            ->query('SELECT * FROM `intervals` WHERE `name` = ? OR `parameter` = ?', \PDO::FETCH_ASSOC);

        $query->execute([ $name, $name ]);
        $result = $query->fetch();

        return \is_array($result) ? $result : null;
    }

    public function requestService(string $name) : ServiceRequest
    {
        $record = $this->getRecord($name);
        if (empty($record)) {
            throw new InvalidInterval($name);
        }

        $interval = new Interval($record);
        $serviceRequest = ServiceRequest::with($interval)->asSingleton();

        if ($name === $interval->getName()) {
            $serviceRequest->withAlias($interval->getParameter());
        }
        elseif ($name === $interval->getParameter()) {
            $serviceRequest->withAlias($interval->getName());
        }

        return $serviceRequest;
    }
}
