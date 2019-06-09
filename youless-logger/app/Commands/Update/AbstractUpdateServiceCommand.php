<?php declare(strict_types=1);

namespace Casa\YouLess\Commands\Update;

use Casa\YouLess\Device\DeviceFactory;
use Casa\YouLess\Request\Request;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractUpdateServiceCommand extends AbstractUpdateCommand
{
    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        parent::execute($input, $output);

        $devices = $this->_getDeviceNames($input);
        foreach ($devices as $device) {
            $device = DeviceFactory::instance()->get($device);
            $service = $this->getServiceName();

            if (!$device->hasActiveService($service)) {
                $output->writeln(\sprintf('Service %s not active for device %s', $service, $device->getName()));
                continue;
            }

            $this->_request($input, $device, $service);
        }
    }

    abstract public function getServiceName() : string;
}
