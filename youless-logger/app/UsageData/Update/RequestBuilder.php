<?php declare(strict_types=1);

namespace Casa\YouLess\UsageData\Update;

use Casa\YouLess\Device\Device;
use Casa\YouLess\UsageData\Interval;
use Casa\YouLess\UsageData\Service;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Stellar\Common\StringUtil;

final class RequestBuilder
{
    use LoggerAwareTrait;

    /** @var Device */
    protected $_device;

    /** @var Service */
    protected $_service;

    /** @var Interval */
    protected $_interval;

    /** @var int[] */
    protected $_pages = [];

    /** @var string[] */
    protected $_pageRanges = [];

    /** @var bool */
    protected $_allPages;

    public function __construct(Device $device, Service $service, ?LoggerInterface $logger = null)
    {
        $this->_device = $device;
        $this->_service = $service;
        $this->logger = $logger ?? new NullLogger();
    }

    public function withInterval(Interval $interval) : self
    {
        $this->_interval = $interval;

        return $this;
    }

    public function withPage(int $page) : self
    {
        $this->_pages[] = $page;

        return $this;
    }

    public function withPageRange(string $pageRange) : self
    {
        $pageRange = StringUtil::unprefix($pageRange, '=');
        $ranges = \explode(',', $pageRange);

        foreach ($ranges as $range) {
            if (false !== \strpos($range, '-')) {
                $this->_pageRanges[] = $range;
                continue;
            }
            if (\is_numeric($range)) {
                $this->_pages[] = (int) $range;
                continue;
            }
        }

        return $this;
    }

    public function withAllPages(bool $allPages = true) : self
    {
        $this->_allPages = $allPages;

        return $this;
    }

    public function getDevice() : Device
    {
        return $this->_device;
    }

    public function getService() : Service
    {
        return $this->_service;
    }

    public function getInterval() : Interval
    {
        return $this->_interval;
    }

    public function getPages() : array
    {
        $maxEnd = $this->_device->getModel()
            ->getIntervalPageRange(
                $this->_service->getName(),
                $this->_interval->getName()
            );

        if ($this->_allPages) {
            return \range(1, $maxEnd);
        }

        if (empty($this->_pageRanges)) {
            return $this->_pages;
        }

        $merge = [ $this->_pages ];
        foreach ($this->_pageRanges as $range) {
            [ $start, $end ] = \explode('-', $range);
            $merge[] = \range($start ?: 1, $end ?: $maxEnd);
        }

        $result = \array_merge(...$merge);
        $result = \array_unique($result);
        \sort($result, \SORT_NUMERIC);

        return $result;
    }

    public function createRequests() : array
    {
        $result = [];

        $endpoint = $this->_service->getEndpoint();
        $parameter = $this->_interval->getParameter();

        $pages = $this->getPages();
        foreach ($pages as $page) {
            $result[] = $request = $this->_device->createRequest($endpoint)
                ->withQueryParam($parameter, (string) $page)
                ->withResponseAs(Response::class);

            $this->logger->debug($request->getUrl());
        }

        return $result;
    }
}

