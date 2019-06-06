<?php declare(strict_types=1);

namespace Casa\YouLess\Commands\Update;

use Casa\YouLess\Config;
use Stellar\Common\ArrayUtil;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractUpdateCommand extends Command
{
    protected const ARG_DEVICE = 'device';

    protected const OPTION_INTERVAL = 'interval';

    protected const OPTION_INTERVAL_ALL = 'all-intervals';

    protected const OPTION_PAGE = 'page';

    protected const OPTION_PAGE_ALL = 'all-pages';

    protected const OPTION_ALL = 'all';

    protected const OPTION_REPLACE = 'replace';

    protected const OPTION_BATCH = 'batch';

    protected const OPTION_SLEEP = 'sleep';

    protected $_batchSize;

    protected $_sleep;

    protected function _getDeviceNames(InputInterface $input) : array
    {
        $devices = $input->getArgument(self::ARG_DEVICE);
        if (empty($devices)) {
            return \array_keys(Config::instance()->devices);
        }

        return ArrayUtil::wrap($devices);
    }

    protected function _applyOptionsToFactory(InputInterface $input, UsageUpdateRequest $request) : void
    {
        if ($input->getOption(self::OPTION_ALL)) {
            $request->withAllIntervals();
            $request->withAllPages();

            return;
        }

        $input->getOption(self::OPTION_INTERVAL_ALL)
            ? $request->withAllIntervals()
            : $request->withInterval($input->getOption(self::OPTION_INTERVAL));

        $input->getOption(self::OPTION_PAGE_ALL)
            ? $request->withAllPages()
            : $request->withPageRange($input->getOption(self::OPTION_PAGE));
    }

    protected function _applyOptionsToRequest(InputInterface $input, UsageUpdateRequest $request) : void
    {
        $request->withBatchSize($this->_batchSize);
        $request->withSleep($this->_sleep);
    }

    protected function configure() : void
    {
        $this->addArgument(
            self::ARG_DEVICE,
            InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
            'Name of the device. Leave empty for all devices'
        );

        $this->addOption(
            self::OPTION_INTERVAL, 'i',
            InputOption::VALUE_OPTIONAL,
            'Which interval to update: min, 10min, hour or day'
        );

        $this->addOption(
            self::OPTION_INTERVAL_ALL, 'I',
            InputOption::VALUE_NONE,
            'All intervals'
        );

        $this->addOption(
            self::OPTION_PAGE, 'p',
            InputOption::VALUE_OPTIONAL,
            'Page(s) or page range',
            1
        );

        $this->addOption(
            self::OPTION_PAGE_ALL, 'P',
            InputOption::VALUE_NONE,
            'All pages'
        );

        $this->addOption(
            self::OPTION_ALL, 'A',
            InputOption::VALUE_NONE,
            'All intervals and all pages'
        );

        $this->addOption(
            self::OPTION_BATCH, 'b',
            InputOption::VALUE_OPTIONAL,
            'Number of executed requests before data is saved in database',
            5
        );

        $this->addOption(
            self::OPTION_SLEEP, 's',
            InputOption::VALUE_OPTIONAL,
            'Amount of seconds to sleep between requests',
            0.5
        );

        $this->addOption(
            self::OPTION_REPLACE, 'r',
            InputOption::VALUE_NONE,
            'Replace existing values'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $this->_batchSize = (int) $input->getOption(self::OPTION_BATCH);
        $this->_sleep = (int) $input->getOption(self::OPTION_SLEEP);
    }
}
