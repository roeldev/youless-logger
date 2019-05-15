<?php declare(strict_types=1);

namespace Casa\YouLess\Commands;

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
        $output->writeln(time());
    }
}
