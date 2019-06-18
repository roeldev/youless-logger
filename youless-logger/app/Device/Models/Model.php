<?php declare(strict_types=1);

namespace Casa\YouLess\Device\Models;

use Stellar\Common\Abilities\StringableTrait;
use Stellar\Container\Abilities\SingletonInstanceTrait;

abstract class Model
{
    use SingletonInstanceTrait;
    use StringableTrait;

    // service => [ interval => pages ]
    protected const SERVICES = [];

    /**
     * Get names of supported services;
     */
    public function getServices() : array
    {
        return \array_keys(static::SERVICES);
    }

    /**
     * Indicates if service is supported.
     */
    public function hasService(string $service) : bool
    {
        return \array_key_exists($service, static::SERVICES);
    }

    /**
     * Get max page range of interval of service, or `null` when not supported.
     */
    public function getIntervalPageRange(string $service, string $interval) : ?int
    {
        if (!\array_key_exists($service, static::SERVICES)
            || !\array_key_exists($interval, static::SERVICES[ $service ])
        ) {
            return null;
        }

        return static::SERVICES[ $service ][ $interval ];
    }
}
