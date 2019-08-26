<?php declare(strict_types=1);

namespace Casa\YouLess\Console\Commands\Update;

use Casa\YouLess\Device\DevicesContainer;
use Casa\YouLess\Request\Request;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends AbstractUpdateCommand
{
    protected static $defaultName = 'update';

    protected function configure() : void
    {
        parent::configure();
        $this->setDescription('Update latest data from YouLess device');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        parent::execute($input, $output);

        $devices = $this->_getDeviceNames($input);
        foreach ($devices as $device) {
            $device = DevicesContainer::instance()->get($device);
            $services = $device->getActiveServices();
            foreach ($services as $service) {
                $this->_request($device, $service);
            }
        }
    }
}
