<?php declare(strict_types=1);

namespace Casa\YouLess;

use Casa\YouLess\Commands\Device\DeviceCommand;
use Casa\YouLess\Commands\Device\DeviceIpCommand;
use Casa\YouLess\Commands\Device\DeviceMacCommand;
use Casa\YouLess\Commands\Device\DeviceModelCommand;
use Casa\YouLess\Commands\Update\UpdateCommand;
use Stellar\Common\Contracts\SingletonInterface;
use Stellar\Container\Registry;
use Symfony\Component\Console\Application;

final class App extends Application implements SingletonInterface
{
    protected const VERSION_FILE = '/youless-logger/version.txt';

    /**
     * @return static
     */
    public static function instance()
    {
        return Registry::singleton(static::class);
    }

    protected function _readVersion() : string
    {
        $result = false;
        if (\file_exists(self::VERSION_FILE)) {
            $result = \file_get_contents(self::VERSION_FILE);
        }

        return $result ?: 'UNKNOWN';
    }

    public function __construct()
    {
        parent::__construct('Logger service for YouLess energy monitor', $this->_readVersion());

        Config::instance();

        $this->addCommands([
            new DeviceCommand(),
            new DeviceIpCommand(),
            new DeviceMacCommand(),
            new DeviceModelCommand(),
            new UpdateCommand(),
        ]);
    }
}
