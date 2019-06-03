<?php declare(strict_types=1);

namespace Casa\YouLess\TimeFrames;

class TimeFrame
{
    protected $_id;

    protected $_name;

    protected $_alias;

    protected $_deltaTime;

    public function __construct(int $id, string $name, string $alias, int $deltaTime)
    {
        $this->_id = $id;
        $this->_name = $name;
        $this->_alias = $alias;
        $this->_deltaTime = $deltaTime;
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

    public function getDeltaTime() : int
    {
        return $this->_deltaTime;
    }
}
