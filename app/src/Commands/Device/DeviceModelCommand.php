<?php declare(strict_types=1);

namespace Casa\YouLess\Commands\Device;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeviceModelCommand extends AbstractDeviceCommand
{
    protected static $defaultName = 'device:model';

    protected function configure() : void
    {
        parent::configure();
        $this->setDescription('Display YouLess device model');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            $this->_getDevice($input)->getModel()
        );
    }
}
