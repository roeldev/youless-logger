<?php declare(strict_types=1);

namespace Casa\YouLess\Response;

use Stellar\Curl\Response\JsonResponse;

class UsageData extends JsonResponse
{
    protected $_unit;

    protected $_startTime;

    protected $_interval;

    protected function _processValues(int $timestamp, array $values)
    {
        $result = [];

        foreach ($values as $value) {
            $result[ date('Y-m-d H:i:s', $timestamp) ] = (int) $value;
            $timestamp += $this->_interval;
        }

        \array_pop($result);

        return $result;
    }

    public function __construct(
        $requestResource,
        array $usedOptions,
        string $response
    ) {
        parent::__construct($requestResource, $usedOptions, $response);

        $this->_unit = $this->_data['unit'];
        $this->_startTime = \strtotime($this->_data['tm']);
        $this->_interval = (int) $this->_data['dt'];
        $this->_data = $this->_processValues($this->_startTime, $this->_data['val']);
    }

    public function getUnit() : string
    {
        return $this->_unit;
    }

    public function getStartTime() : int
    {
        return $this->_startTime;
    }

    public function getTimeInterval() : int
    {
        return $this->_interval;
    }
}
