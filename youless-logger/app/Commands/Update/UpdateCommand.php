<?php declare(strict_types=1);

namespace Casa\YouLess\Commands\Update;

use Casa\YouLess\Database\UsageDataTransaction;
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
        for ($i=1; $i<=30; $i++) {
            /** @var UsageData $response */
            $response = Request::updatePower('w', $i)->response();

            print_r($response->getValues());

            (new UsageDataTransaction($response))->save();
            sleep(5);
        }
        for ($i=1; $i<=70; $i++) {
            /** @var UsageData $response */
            $response = Request::updatePower('d', $i)->response();

            print_r($response->getValues());

            (new UsageDataTransaction($response))->save();
            sleep(5);
        }
        for ($i=1; $i<=12; $i++) {
            /** @var UsageData $response */
            $response = Request::updatePower('m', $i)->response();

            print_r($response->getValues());

            (new UsageDataTransaction($response))->save();
            sleep(5);
        }
    }
}
