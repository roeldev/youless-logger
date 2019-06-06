<?php declare(strict_types=1);

namespace Casa\YouLess\Commands\Update;

use Casa\YouLess\Database\UsageDataTransaction;
use Casa\YouLess\Device\DeviceFactory;
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

    protected function _request(InputInterface $input, DeviceInterface $device, string $type)
    {
        // $request = UsageDataFactory::instance()->createRequest($device, 'power');

        $factory = UsageDataRequestFactory::instance()
            ->create($type, $device);

        $this->_applyOptionsToFactory($input, $factory);

        // create Stellar\Curl\Request\MultiRequest
        // with Requests to all endpoints according to options
        $request = $factory->request()
            ->onBatchReach([ $this, '_saveData' ]);

        $this->_applyOptionsToRequest($input, $request);
        $request->execute();
    }

    protected function _saveData(array $responses) : void
    {
        foreach ($responses as $response) {
            (new UsageDataTransaction($response))->save();
        }

        \sleep($this->_sleep);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        parent::execute($input, $output);

        $devices = $this->_getDeviceNames($input);
        foreach ($devices as $device) {
            $device = DeviceFactory::instance()->get($device);
            $services = $device->getActiveServices();
            foreach ($services as $service) {
                print_r([ $device->getName(), $service, $device->getModel()->getServicePages($service) ]);
                // $this->_request($input, $device, $type);
            }
        }
    }
}
