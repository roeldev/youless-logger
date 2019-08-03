<?php declare(strict_types=1);

namespace Casa\YouLess\Console\Commands\Device;

use Casa\YouLess\Device\Device;
use Casa\YouLess\Device\DeviceFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

abstract class AbstractDeviceCommand extends Command
{
    protected function _getDevice(InputInterface $input) : Device
    {
        return DeviceFactory::instance()
            ->get($input->getArgument('name'));
    }

    protected function configure() : void
    {
        $this->addArgument(
            'name',
            InputArgument::OPTIONAL,
            'Name of the device',
            'default'
        );
    }
}
