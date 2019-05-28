<?php declare(strict_types=1);

namespace Casa\YouLess\Response;

use Stellar\Curl\Response\JsonResponse;

class UsageData extends JsonResponse
{
    public const DELTAS = [
        60 => 'min',
        600 => '10mins',
        3600 => 'hour',
        86400 => 'day',
    ];

    /** @var string */
    protected $_unit;

    /** @var int */
    protected $_startTime;

    /** @var int */
    protected $_deltaTime;

    /** @var array<int, int> */
    protected $_values;

    protected function _processValues(int $timestamp, array $values)
    {
        $result = [];

        foreach ($values as $value) {
            if ('*' === $value) {
                continue;
            }

            $result[ $timestamp ] = (int) $value;
            $timestamp += $this->_deltaTime;
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

        $this->_unit = \trim($this->_data['un']);
        $this->_startTime = \strtotime($this->_data['tm']);
        $this->_deltaTime = (int) $this->_data['dt'];
        $this->_values = $this->_processValues($this->_startTime, $this->_data['val']);
    }

    public function getUnit() : string
    {
        return $this->_unit;
    }

    public function getStartTime() : int
    {
        return $this->_startTime;
    }

    public function getDelta() : ?string
    {
        return self::DELTAS[ $this->_deltaTime ] ?? null;
    }

    public function getDeltaTime() : int
    {
        return $this->_deltaTime;
    }

    public function getValues() : array
    {
        return $this->_values;
    }
}
