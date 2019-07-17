<?php declare(strict_types=1);

namespace Casa\YouLess\Device\Models;

final class LS120 extends Model
{
    // service => [ interval => pages ]
    protected const SERVICES = [
        'power' => [
            'min' => 20,
            '10min' => 30,
            'hour' => 70,
            'day' => 12,
        ],

        'gas' => [
            '10min' => 30,
            'hour' => 70,
            'day' => 12,
        ],

        's0' => [
            'min' => 20,
            '10min' => 30,
            'hour' => 70,
            'day' => 12,
        ],
    ];

    /** {@inheritDoc} */
    public function __toString() : string
    {
        return 'LS120';
    }
}
