<?php declare(strict_types=1);

namespace Casa\YouLess\UsageData;

use Casa\YouLess\Database;
use Casa\YouLess\Exceptions\InvalidService;
use Stellar\Container\Abilities\SingletonInstanceTrait;
use Stellar\Container\ServiceRequest;

final class ServiceRegistry extends AbstractRegistry
{
    use SingletonInstanceTrait;

    public function get(string $name) : Service
    {
        return $this->_container->request($name, [ $this, 'requestService' ], [ $name ]);
    }

    public function getRecord(string $name) : ?array
    {
        $query = Database::instance()
            ->query('SELECT * FROM `services` WHERE `name` = ? OR `endpoint` = ?', \PDO::FETCH_ASSOC);

        $query->execute([ $name, $name ]);
        $result = $query->fetch();

        return \is_array($result) ? $result : null;
    }

    public function requestService(string $name) : ServiceRequest
    {
        $record = $this->getRecord($name);
        if (empty($record)) {
            throw new InvalidService($name);
        }

        $service = new Service($record);
        $serviceRequest = ServiceRequest::with($service)->asSingleton();

        if ($name === $service->getName()) {
            $serviceRequest->withAlias($service->getEndpoint());
        }
        elseif ($name === $service->getEndpoint()) {
            $serviceRequest->withAlias($service->getName());
        }

        return $serviceRequest;
    }
}
