<?php declare(strict_types=1);

namespace Casa\YouLess\Interval;

use Casa\YouLess\Database;
use Stellar\Container\Container;
use Stellar\Container\Registry;
use Stellar\Container\ServiceRequest;
use Stellar\Container\Traits\SingletonInstanceTrait;

final class IntervalFactory
{
    use SingletonInstanceTrait;

    /** @var Container */
    protected $_container;

    protected function _requestService(array $record) : ServiceRequest
    {
        $interval = new Interval(
            (int) $record['id'],
            $record['name'],
            $record['alias']
        );

        return ServiceRequest::with($interval)
            ->asSingleton()
            ->withAlias($interval->getAlias());
    }

    public function __construct()
    {
        $this->_container = Registry::container(self::class);
    }

    public function init() : void
    {
        $query = Database::instance()->query('SELECT * FROM `intervals`', \PDO::FETCH_ASSOC);
        $query->execute();

        $requestServiceFn = \Closure::fromCallable([ $this, '_requestService' ]);
        foreach ($query->fetchAll() as $record) {
            $this->_container->request($record['name'], $requestServiceFn, $record);
        }
    }

    public function fromName(string $name) : Interval
    {
        return $this->_container->get($name);
    }

    public function fromAlias(string $alias) : Interval
    {
        return $this->_container->get($alias);
    }
}
