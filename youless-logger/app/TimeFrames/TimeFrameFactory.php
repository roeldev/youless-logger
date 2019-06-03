<?php declare(strict_types=1);

namespace Casa\YouLess\TimeFrames;

use Casa\YouLess\Database;
use Stellar\Container\Container;
use Stellar\Container\Registry;
use Stellar\Container\ServiceRequest;
use Stellar\Container\Traits\SingletonInstanceTrait;

final class TimeFrameFactory
{
    use SingletonInstanceTrait;

    /** @var Container */
    protected $_container;

    public function __construct()
    {
        $this->_container = Registry::container(self::class);
    }

    public function fromName(string $name) : TimeFrame
    {
        return $this->_container->request($name, function () use ($name) {
            $query = Database::instance()->prepare('SELECT * FROM `timeframes` WHERE `name` = ?');
            $query->execute([ $name ]);
            $record = $query->fetch();

            $service = new TimeFrame(
                (int) $record['id'],
                $name,
                $record['alias'],
                (int) $record['deltatime']
            );

            return ServiceRequest::with($service)
                // ->addAlias('id', $service->getId())
                // ->addAlias('alias', $service->getAlias())
                // ->addAlias('deltatime', $service->getDeltaTime())
                ->asSingleton();
        });
    }
}
