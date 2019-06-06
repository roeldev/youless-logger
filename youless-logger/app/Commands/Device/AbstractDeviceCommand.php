<?php declare(strict_types=1);

namespace Casa\YouLess\Commands\Device;

use Casa\YouLess\Devices\DeviceFactory;
use Casa\YouLess\Devices\DeviceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

abstract class AbstractDeviceCommand extends Command
{
    protected function _getDevice(InputInterface $input) : DeviceInterface
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
