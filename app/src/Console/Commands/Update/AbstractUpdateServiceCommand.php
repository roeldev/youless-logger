<?php declare(strict_types=1);

namespace Casa\YouLess\Console\Commands\Update;

use Casa\YouLess\Device\DeviceFactory;
use Casa\YouLess\Request\Request;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractUpdateServiceCommand extends AbstractUpdateCommand
{
    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        parent::execute($input, $output);

        $service = $this->getServiceName();
        $devices = $this->_getDeviceNames();

        foreach ($devices as $device) {
            $device = DeviceFactory::instance()->get($device);
            if (!$device->hasActiveService($service)) {
                $output->writeln(\sprintf('Service `%s` is not active for device `%s`', $service, $device->getName()));
                continue;
            }

            $this->_request($device, $service);
        }
    }

    abstract public function getServiceName() : string;
}
