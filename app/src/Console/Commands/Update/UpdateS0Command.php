<?php declare(strict_types=1);

namespace Casa\YouLess\Console\Commands\Update;

final class UpdateS0Command extends AbstractUpdateServiceCommand
{
    protected static $defaultName = 'update:s0';

    protected function configure() : void
    {
        parent::configure();
        $this->setDescription('Update s0 counter data from YouLess device');
    }

    public function getServiceName() : string
    {
        return 's0';
    }
}
