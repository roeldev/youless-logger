<?php declare(strict_types=1);

namespace Casa\YouLess\Device\Models;

use Stellar\Common\Abilities\StringableTrait;
use Stellar\Container\Abilities\SingletonInstanceTrait;

final class LS110 implements ModelInterface
{
    use SingletonInstanceTrait;
    use StringableTrait;

    /** {@inheritDoc} */
    public function getServices() : array
    {
        return [ 'power' ];
    }

    /** {@inheritDoc} */
    public function getServicesPages() : array
    {
        return [
            'power' => [
                'h' => 2,
                'w' => 3,
                'd' => 7,
                'm' => 12,
            ],
        ];
    }

    /** {@inheritDoc} */
    public function getServicePages(string $service) : array
    {
        return $this->getServicesPages()[ $service ] ?? [];
    }

    /** {@inheritDoc} */
    public function __toString() : string
    {
        return 'LS120';
    }
}
