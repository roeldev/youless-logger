<?php declare(strict_types=1);

namespace Casa\YouLess\UsageData;

use Stellar\Common\Abilities\StringableTrait;
use Stellar\Common\Contracts\ArrayableInterface;
use Stellar\Common\Contracts\StringableInterface;

final class Unit implements ArrayableInterface, StringableInterface
{
    use StringableTrait;

    private $_id;

    private $_name;

    private $_alias;

    public function __construct(array $record)
    {
        $this->_id = (int) $record['id'];
        $this->_name = $record['name'];
        $this->_alias = $record['alias'] ?? $record['name'];
    }

    public function getId() : int
    {
        return $this->_id;
    }

    public function getName() : string
    {
        return $this->_name;
    }

    public function getAlias() : string
    {
        return $this->_alias;
    }

    public function toArray() : array
    {
        return [
            'id' => $this->_id,
            'name' => $this->_name,
            'alias' => $this->_alias,
        ];
    }

    public function __toString() : string
    {
        return $this->_alias;
    }
}
