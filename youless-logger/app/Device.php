<?php declare(strict_types=1);

namespace Casa\YouLess;

use Stellar\Common\Contracts\SingletonInterface;
use Stellar\Container\Registry;

final class Device implements SingletonInterface
{
    /**
     * @return static
     */
    public static function instance()
    {
        return Registry::singleton(static::class);
    }

    public function getHost() : ?string
    {
        $host = \getenv('YOULESS_HOST');
        if (!$host || !\is_string($host)) {
            return null;
        }

        return \rtrim($host, '/');
    }

    public function getIp() : ?string
    {
        $host = $this->getHost();
        if (!$host) {
            return null;
        }

        return \str_replace('http://', '', \gethostbyname($host));
    }
}
