<?php declare(strict_types=1);

namespace Casa\YouLess\UsageData;

use Stellar\Common\Abilities\StringableTrait;
use Stellar\Common\Contracts\ArrayableInterface;
use Stellar\Common\Contracts\StringableInterface;

final class Service implements ArrayableInterface, StringableInterface
{
    use StringableTrait;

    private $_id;

    private $_name;

    private $_endpoint;

    public function __construct(array $record)
    {
        $this->_id = (int) $record['id'];
        $this->_name = $record['name'];
        $this->_endpoint = $record['endpoint'];
    }

    public function getId() : int
    {
        return $this->_id;
    }

    public function getName() : string
    {
        return $this->_name;
    }

    public function getEndpoint() : string
    {
        return $this->_endpoint;
    }

    public function toArray() : array
    {
        return [
            'id' => $this->_id,
            'name' => $this->_name,
            'endpoint' => $this->_endpoint,
        ];
    }

    public function __toString() : string
    {
        return $this->_name;
    }
}
