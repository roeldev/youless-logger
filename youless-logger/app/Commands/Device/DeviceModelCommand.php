<?php declare(strict_types=1);

namespace Casa\YouLess\Commands\Device;

use Casa\YouLess\Request\Request;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeviceModelCommand extends Command
{
    protected static $defaultName = 'device:model';

    protected function configure()
    {
        $this->setDescription('Display YouLess device model');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(Request::deviceInfo()->response()->model);
    }
}
