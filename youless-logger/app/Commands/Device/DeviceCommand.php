<?php declare(strict_types=1);

namespace Casa\YouLess\Commands\Device;

use Casa\YouLess\Device\DeviceFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeviceCommand extends Command
{
    protected static $defaultName = 'device';

    protected function configure()
    {
        $this->setDescription('Display info of the YouLess device');
        $this->addArgument('name', null, 'Name of the device', 'default');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $deviceInfo = DeviceFactory::instance()
            ->get($input->getArgument('name'))
            ->toArray();

        foreach ($deviceInfo as $key => $value) {
            $output->writeln(sprintf('%s: %s', $key, $value));
        }
    }
}
