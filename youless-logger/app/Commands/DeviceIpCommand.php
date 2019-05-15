<?php declare(strict_types=1);

namespace Casa\YouLess\Commands;

use Casa\YouLess\Device;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeviceIpCommand extends Command
{
    protected static $defaultName = 'device:ip';

    protected function configure()
    {
        $this->setDescription('Display IP address of YouLess device');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(Device::getIp());
    }
}
