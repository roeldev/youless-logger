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

    protected function _getRecord(string $name) : array
    {
        $query = Database::instance()
            ->query('SELECT * FROM `intervals` WHERE `name` = ? OR `parameter` = ?', \PDO::FETCH_ASSOC);

        $query->execute([ $name, $name ]);
        $result = $query->fetch();

        return \is_array($result) ? $result : [];
    }

    public function __construct()
    {
        $this->_container = Registry::container(self::class);
    }

    public function get(string $name) : Interval
    {
        $name = StringUtil::unprefix($name, '=');

        return $this->_container->request($name, [ $this, 'requestService' ], [ $name ]);
    }

    public function requestService(string $name) : ServiceRequest
    {
        $record = $this->_getRecord($name);
        if (empty($record)) {
            throw new InvalidInterval($name);
        }

        $interval = new Interval($record);

        return ServiceRequest::with($interval)
            ->asSingleton()
            ->withAlias($interval->getParameter());
    }
}
