<?php declare(strict_types=1);

namespace Casa\YouLess\Interval;

use Casa\YouLess\Database;
use Casa\YouLess\Exceptions\InvalidInterval;
use Stellar\Common\StringUtil;
use Stellar\Container\Abilities\SingletonInstanceTrait;
use Stellar\Container\Container;
use Stellar\Container\Registry;
use Stellar\Container\ServiceRequest;

final class IntervalFactory
{
    use SingletonInstanceTrait;

    /** @var Container */
    protected $_container;

    protected function _requestService(string $name) : ServiceRequest
    {
        $query = Database::instance()
            ->query('SELECT * FROM `intervals` WHERE `name` = ? OR `alias` = ?', \PDO::FETCH_ASSOC);

        $query->execute([ $name, $name ]);
        $record = $query->fetch();

        if (empty($record)) {
            throw new InvalidInterval($name);
        }

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

    public function get(string $name) : Interval
    {
        $name = StringUtil::unprefix($name, '=');

        return $this->_container->request(
            $name,
            \Closure::fromCallable([ $this, '_requestService' ]),
            $name
        );
    }
}
