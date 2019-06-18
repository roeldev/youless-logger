<?php declare(strict_types=1);

namespace Casa\YouLess\Commands\Update;

final class UpdatePowerCommand extends AbstractUpdateServiceCommand
{
    protected static $defaultName = 'update:power';

    protected function configure() : void
    {
        parent::configure();
        $this->setDescription('Update power usage data from YouLess device');
    }

    public function getServiceName() : string
    {
        return 'power';
    }
}
