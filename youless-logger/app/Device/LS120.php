<?php declare(strict_types=1);

namespace Casa\YouLess\Device;

class LS120 extends LS110
{
    protected $_mac;

    /** {@inheritDoc} */
    public static function getPowerRange() : array
    {
        return [
            'h' => 20,
            'w' => 30,
            'd' => 70,
            'm' => 12,
        ];
    }

    /** {@inheritDoc} */
    public static function getGasRange() : array
    {
        return [
            'w' => 30,
            'd' => 70,
            'm' => 12,
        ];
    }

    /** {@inheritDoc} */
    public static function getS0Range() : array
    {
        return [
            'h' => 20,
            'w' => 30,
            'd' => 70,
            'm' => 12,
        ];
    }
    /** {@inheritDoc} */
    public function getModel() : string
    {
        return 'LS120';
    }
}
