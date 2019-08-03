<?php declare(strict_types=1);

namespace Casa\YouLess\Console;

use Casa\YouLess\Boot\Boot;
use Casa\YouLess\Console\Commands\Device\DeviceCommand;
use Casa\YouLess\Console\Commands\Device\DeviceIpCommand;
use Casa\YouLess\Console\Commands\Device\DeviceMacCommand;
use Casa\YouLess\Console\Commands\Device\DeviceModelCommand;
use Casa\YouLess\Console\Commands\Update\UpdateCommand;
use Casa\YouLess\Console\Commands\Update\UpdateGasCommand;
use Casa\YouLess\Console\Commands\Update\UpdatePowerCommand;
use Casa\YouLess\Console\Commands\Update\UpdateS0Command;
use Symfony\Component\Console\Application;

final class App extends Application
{
    protected const VERSION_FILE = '/app/version.txt';

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

        Boot::execute();

        $this->addCommands([
            new DeviceCommand(),
            new DeviceIpCommand(),
            new DeviceMacCommand(),
            new DeviceModelCommand(),
            new UpdateCommand(),
            new UpdatePowerCommand(),
            new UpdateGasCommand(),
            new UpdateS0Command(),
        ]);
    }
}
