<?php declare(strict_types=1);

namespace Casa\YouLess\Api;

use Symfony\Component\Routing\RouteCollection;

interface ControllerInterface
{
    public function getRouteCollection() : RouteCollection;
}
