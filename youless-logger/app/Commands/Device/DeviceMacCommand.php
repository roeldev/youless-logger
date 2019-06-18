<?php declare(strict_types=1);

namespace Casa\YouLess\Commands\Device;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeviceMacCommand extends AbstractDeviceCommand
{
    protected static $defaultName = 'device:mac';

    protected function configure() : void
    {
        parent::configure();
        $this->setDescription('Display mac address of YouLess device');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            $this->_getDevice($input)->getMac()
        );
    }
}
