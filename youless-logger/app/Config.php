<?php declare(strict_types=1);

namespace Casa\YouLess;

use Casa\YouLess\Device\DeviceFactory;
use Stellar\Common\Contracts\SingletonInterface;
use Stellar\Container\Registry;
use Symfony\Component\Yaml\Yaml;

final class Config implements SingletonInterface
{
    /**
     * @return static
     */
    public static function instance()
    {
        return Registry::singleton(static::class);
    }

    protected $_config;

    public function __construct()
    {
        $this->_config = Yaml::parseFile('/youless-logger/config/config.yml');

        foreach ($this->_config['devices'] as $name => $settings) {
            DeviceFactory::instance()->create($name, $settings);
        }
    }
}
