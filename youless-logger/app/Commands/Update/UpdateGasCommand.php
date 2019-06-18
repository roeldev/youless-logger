<?php declare(strict_types=1);

namespace Casa\YouLess\Commands\Update;

final class UpdateGasCommand extends AbstractUpdateServiceCommand
{
    protected static $defaultName = 'update:gas';

    protected function configure() : void
    {
        parent::configure();
        $this->setDescription('Update gas usage data from YouLess device');

        $intervalOption = $this->getDefinition()->getOption(self::OPTION_INTERVAL);
        $intervalOption->setDefault('10min');
    }

    public function getServiceName() : string
    {
        return 'gas';
    }
}
