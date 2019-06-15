<?php declare(strict_types=1);

namespace Casa\YouLess\Commands\Update;

use Casa\YouLess\Config;
use Casa\YouLess\Device\Device;
use Casa\YouLess\UsageData\IntervalFactory;
use Casa\YouLess\UsageData\Update\RequestBuilder;
use Casa\YouLess\UsageData\Update\Response;
use Casa\YouLess\UsageData\Update\Transaction;
use Stellar\Common\ArrayUtil;
use Stellar\Curl\Request\Request;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractUpdateCommand extends Command
{
    protected const ARG_DEVICE = 'device';

    protected const OPTION_INTERVAL = 'interval';

    protected const OPTION_PAGE = 'page';

    protected const OPTION_ALL_PAGES = 'all-pages';

    protected const OPTION_REPLACE = 'replace';

    protected const OPTION_BATCH = 'batch';

    protected const OPTION_SLEEP = 'sleep';

    /** @var InputInterface */
    protected $_input;

    /** @var OutputInterface */
    protected $_output;

    /** @var int */
    protected $_batchSize;

    /** @var float */
    protected $_sleep;

    /** @var bool */
    protected $_replace = false;

    protected function _getDeviceNames() : array
    {
        $devices = $this->_input->getArgument(self::ARG_DEVICE);
        if (empty($devices)) {
            return \array_keys(Config::instance()->devices);
        }

        return ArrayUtil::wrap($devices);
    }

    protected function _applyOptions(RequestBuilder $builder, InputInterface $input) : void
    {
        $builder->withInterval(
            IntervalFactory::instance()->get($input->getOption(self::OPTION_INTERVAL))
        );

        if ($input->getOption(self::OPTION_ALL_PAGES)) {
            $builder->withAllPages();
        }
        else {
            $page = $input->getOption(self::OPTION_PAGE);
            is_string($page)
                ? $builder->withPageRange($page)
                : $builder->withPage((int) $page);
        }
    }

    protected function _request(Device $device, string $service) : void
    {
        $this->_output->writeln(\sprintf('Updating service `%s` of device `%s`', $service, $device->getName()));

        $builder = new RequestBuilder($device, $service);
        $this->_applyOptions($builder, $this->_input);

        $requests = $builder->createRequests();
        if (empty($requests)) {
            return;
        }

        if (1 === count($requests)) {
            /** @var Request $request */
            $request = $requests[0];

            $this->_output->writeln(
                \sprintf('executing... %s', $request->getUrl()),
                OutputInterface::VERBOSITY_VERY_VERBOSE
            );

            $this->_processRequest($request->execute());

            return;
        }

        $batches = \array_chunk($requests, $this->_batchSize);
        print_r($batches);

        // while (true) {
        //     Curl::multi(...\array_shift($batches))
        //         ->execute()
        //         ->forEach(\Closure::fromCallable([ $this, '_processRequest' ]))
        //         ->close();
        //
        //     if (empty($batches)) {
        //         break;
        //     }
        //
        //     \sleep($this->_sleep);
        // }
    }

    protected function _processRequest(Request $request) : void
    {
        $this->_output->writeln(
            \sprintf('processing... %s', $request->getRawResponse()),
            OutputInterface::VERBOSITY_DEBUG
        );

        /** @var Response $response */
        $response = $request->response();

        print_r($response);

        // (new Transaction($response))->commit();
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
            'Which interval to update: min, 10min, hour or day',
            'min'
        );

        $this->addOption(
            self::OPTION_PAGE, 'p',
            InputOption::VALUE_OPTIONAL,
            'Page(s) or page range',
            1
        );

        $this->addOption(
            self::OPTION_ALL_PAGES, 'P',
            InputOption::VALUE_NONE,
            'All pages'
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
        $this->_input = $input;
        $this->_output = $output;
        $this->_batchSize = (int) $input->getOption(self::OPTION_BATCH);
        $this->_sleep = (int) $input->getOption(self::OPTION_SLEEP);
    }
}
