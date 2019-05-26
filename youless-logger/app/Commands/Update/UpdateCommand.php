<?php declare(strict_types=1);

namespace Casa\YouLess\Commands\Update;

use Casa\YouLess\Request\Request;
use Casa\YouLess\Response\UsageData;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends Command
{
    protected static $defaultName = 'update';

    protected function configure()
    {
        $this->setDescription('Update latest data from YouLess device');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var UsageData $response */
        $response = Request::updatePower()->response();
        print_r($response->toArray());
    }
}
