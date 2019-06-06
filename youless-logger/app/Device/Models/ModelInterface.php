<?php declare(strict_types=1);

namespace Casa\YouLess\Device\Models;

use Stellar\Common\Contracts\SingletonInterface;
use Stellar\Common\Contracts\StringableInterface;

interface ModelInterface extends SingletonInterface, StringableInterface
{
    public function getServices() : array;

    public function getServicesPages() : array;

    public function getServicePages(string $service) : array;
}
