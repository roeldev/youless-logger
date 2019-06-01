<?php declare(strict_types=1);

namespace Casa\YouLess\Device;

use Stellar\Container\AbstractFactory;

final class DeviceFactory extends AbstractFactory
{
    public function get(string $name = 'default') : DeviceInterface
    {
        return new LS120($name);
    }
}
