<?php declare(strict_types=1);

namespace Casa\YouLess\UsageData;

use Stellar\Common\Arrayify;
use Stellar\Common\Contracts\ArrayableInterface;

final class Interval implements ArrayableInterface
{
    protected $_id;

    protected $_name;

    protected $_parameter;

    public function __construct(array $record)
    {
        $this->_id = (int) $record['id'];
        $this->_name = $record['name'];
        $this->_parameter = $record['parameter'];
    }

    public function getId() : int
    {
        return $this->_id;
    }

    public function getName() : string
    {
        return $this->_name;
    }

    public function getParameter() : string
    {
        return $this->_parameter;
    }

    public function toArray() : array
    {
        return Arrayify::any($this);
    }
}
