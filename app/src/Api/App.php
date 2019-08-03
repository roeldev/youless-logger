<?php declare(strict_types=1);

namespace Casa\YouLess\Api;

use Casa\YouLess\Boot\Boot;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

final class App
{
    /** @var RouteCollection */
    protected $_routes;

    public function __construct(RouteCollection $routes = null)
    {
        Boot::execute();

        $this->_routes = $routes ?? new RouteCollection();
    }

    public function addActions(string ...$actions) : self
    {
        foreach ($actions as $action) {
            if (!\is_a($action, ActionInterface::class, true)) {
                continue;
            }

            $this->_routes->add($action, \call_user_func($action . '::createRoute'));
        }

        return $this;
    }

    public function run() : void
    {
        $request = Request::createFromGlobals();
        $context = new RequestContext();
        $context->fromRequest($request);

        try {
            $match = (new UrlMatcher($this->_routes, $context))
                ->match($request->getRequestUri());

            $action = $match['_route'] ?? null;
            if ($action && \is_a($action, ActionInterface::class, true)) {
                $response = new JsonResponse();

                /** @var ActionInterface $action */
                $action = new $action();
                $action->execute($request, $response);

                $response->send();
                exit;
            }
        }
        catch (\Exception $e) {
            echo '<pre>';
            print_r($e);
            exit;
        }
    }
}
