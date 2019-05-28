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

class App extends Application implements SingletonInterface
{
    /**
     * @return static
     */
    public static function instance()
    {
        return Registry::singleton(static::class);
    }

    public function __construct()
    {
        parent::__construct('Logger service for YouLess energy monitor', 'v1.0.0');

        $this->addCommands([
            new DeviceCommand(),
            new DeviceIpCommand(),
            new DeviceMacCommand(),
            new DeviceModelCommand(),
            new UpdateCommand(),
        ]);
    }
}
