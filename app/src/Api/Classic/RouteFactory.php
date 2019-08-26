<?php declare(strict_types=1);

namespace Casa\YouLess\Api\Classic;

use Stellar\Common\Contracts\SingletonInterface;
use Stellar\Container\Abilities\SingletonInstanceTrait;
use Symfony\Component\Routing\Route;

final class RouteFactory implements SingletonInterface
{
    use SingletonInstanceTrait;

    public function create(string $path, callable $action, bool $matchQuery = false) : Route
    {
        $route = new Route($path);
        $route->setMethods('GET');
        $route->setDefault('_action', $action);

        if ($matchQuery) {
            $route->setPath($path . '{query}');
            $route->setRequirement('query', '(\?.+)?');
        }

        return $route;
    }

    public function createRedirect(string $path, callable $action) : Route
    {
        return $this->create($path, $action, true);
    }
}
