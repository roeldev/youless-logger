<?php declare(strict_types=1);

namespace Casa\YouLess\Commands\Device;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeviceCommand extends AbstractDeviceCommand
{
    protected static $defaultName = 'device';

    protected function configure() : void
    {
        parent::configure();
        $this->setDescription('Display info of the YouLess device');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $deviceInfo = $this->_getDevice($input)->toArray();
        foreach ($deviceInfo as $key => $value) {
            $output->writeln(sprintf('%s: %s', $key, $value));
        }
    }
}
