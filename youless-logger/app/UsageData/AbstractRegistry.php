<?php declare(strict_types=1);

namespace Casa\YouLess\UsageData;

use Stellar\Common\Contracts\SingletonInterface;
use Stellar\Container\Abilities\SingletonInstanceTrait;
use Stellar\Container\Container;
use Stellar\Container\Registry;
use Stellar\Container\ServiceRequest;

abstract class AbstractRegistry implements SingletonInterface
{
    use SingletonInstanceTrait;

    /** @var Container */
    protected $_container;

    public function __construct()
    {
        $this->_container = Registry::container(static::class);
    }

    abstract public function get(string $name);

    abstract public function getRecord(string $name) : ?array;

    abstract public function requestService(string $name) : ServiceRequest;
}
