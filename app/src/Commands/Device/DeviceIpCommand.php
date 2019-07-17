<?php declare(strict_types=1);

namespace Casa\YouLess\Commands\Device;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeviceIpCommand extends AbstractDeviceCommand
{
    protected static $defaultName = 'device:ip';

    protected function configure() : void
    {
        parent::configure();
        $this->setDescription('Display IP address of YouLess device');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            $this->_getDevice($input)->getIp()
        );
    }
}
