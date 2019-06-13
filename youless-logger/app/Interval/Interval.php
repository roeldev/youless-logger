<?php declare(strict_types=1);

namespace Casa\YouLess\Interval;

use Stellar\Common\Arrayify;
use Stellar\Common\Contracts\ArrayableInterface;

final class Interval implements ArrayableInterface
{
    protected $_id;

    protected $_name;

    protected $_alias;

    public function __construct(int $id, string $name, string $alias)
    {
        $this->_id = $id;
        $this->_name = $name;
        $this->_alias = $alias;
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
        return Arrayify::any($this);
    }
}
