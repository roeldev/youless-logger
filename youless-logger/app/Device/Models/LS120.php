<?php declare(strict_types=1);

namespace Casa\YouLess\Device\Models;

use Stellar\Common\Traits\ToString;
use Stellar\Container\Traits\SingletonInstanceTrait;

final class LS120 implements ModelInterface
{
    use SingletonInstanceTrait;
    use ToString;

    /** {@inheritDoc} */
    public function getServices() : array
    {
        return [ 'power', 'gas', 's0' ];
    }

    /** {@inheritDoc} */
    public function getServicePages() : array
    {
        return [
            'power' => [
                'h' => 20,
                'w' => 30,
                'd' => 70,
                'm' => 12,
            ],

            'gas' => [
                'w' => 30,
                'd' => 70,
                'm' => 12,
            ],

            's0' => [
                'h' => 20,
                'w' => 30,
                'd' => 70,
                'm' => 12,
            ],
        ];
    }

    /** {@inheritDoc} */
    public function __toString() : string
    {
        return 'LS120';
    }
}
