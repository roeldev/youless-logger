<?php declare(strict_types=1);

namespace Casa\YouLess\Device\Models;

final class LS110 extends Model
{
    // service => [ interval => pages ]
    protected const SERVICES = [
        'power' => [
            'min' => 2,
            '10min' => 3,
            'hour' => 7,
            'day' => 12,
        ],
    ];

    /** {@inheritDoc} */
    public function __toString() : string
    {
        return 'LS120';
    }
}
