<?php declare(strict_types=1);

namespace Casa\YouLess\Commands;

use Casa\YouLess\Request\Request;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeviceCommand extends Command
{
    protected static $defaultName = 'device';

    protected function configure()
    {
        $this->setDescription('Display info of the YouLess device');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = Request::deviceInfo()->response();

        $output->writeln('model: ' . $response->model);
        $output->writeln('ip: ' . $response->ip);
        $output->writeln('mac: ' . $response->mac);
    }
}
