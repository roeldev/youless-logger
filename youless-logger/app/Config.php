<?php declare(strict_types=1);

namespace Casa\YouLess;

use Casa\YouLess\Device\DeviceFactory;
use Stellar\Common\Contracts\SingletonInterface;
use Stellar\Container\Registry;

/**
 * @property-read array $devices
 * @property-read array $categories
 * @property-read array $classicApi
 */
final class Config implements SingletonInterface
{
    /**
     * @return static
     */
    public static function instance()
    {
        return Registry::singleton(static::class);
    }

    /** @var array */
    protected $_config;

    protected function _get_devices() : array
    {
        return $this->_config['devices'] ?? [];
    }

    protected function _get_categories() : array
    {
        return $this->_config['categories'] ?? [];
    }

    protected function _get_classicApi() : array
    {
        return $this->_config['classic_api'] ?? [];
    }

    public function __get($config)
    {
        $method = '_get_' . $config;
        if (\method_exists($this, $method)) {
            return $this->$method();
        }

        throw new \OutOfRangeException();
    }

    public function __construct()
    {
        $this->_config = include '/youless-logger/config/config.php';

        foreach ($this->devices as $name => $settings) {
            DeviceFactory::instance()->create($name, $settings);
        }
    }
}
