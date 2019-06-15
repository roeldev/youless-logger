<?php declare(strict_types=1);

namespace Casa\YouLess\UsageData\Update;

use Casa\YouLess\Device\Device;
use Casa\YouLess\UsageData\Interval;
use Stellar\Common\StringUtil;

final class RequestBuilder
{
    protected const SERVICE_ENDPOINTS = [
        'power' => 'V',
        'gas' => 'W',
        's0' => 'Z',
    ];

    /** @var Device */
    protected $_device;

    /** @var string */
    protected $_service;

    /** @var Interval */
    protected $_interval;

    /** @var int[] */
    protected $_pages = [];

    /** @var string[] */
    protected $_pageRanges = [];

    /** @var bool */
    protected $_allPages;

    public function __construct(Device $device, string $service)
    {
        $this->_device = $device;
        $this->_service = $service;
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
            if (\strpos($range, '-')) {
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

    public function createRequests() : array
    {
        $result = [];

        $endpoint = self::SERVICE_ENDPOINTS[ $this->_service ];
        $intervalParam = $this->_interval->getParameter();
        $servicePages = $this->_device->getModel()->getServicePages($this->_service);
        $servicePages = $servicePages[ $intervalParam ];

        if ($this->_allPages) {
            $pages = range(1, $servicePages);
        }
        else {
            $pages = $this->_pages;
        }

        foreach ($pages as $page) {
            $result[] = $this->_device->createRequest($endpoint)
                ->withQueryParam($intervalParam, (string) $page)
                ->withResponseAs(Response::class);
        }

        return $result;
    }
}

