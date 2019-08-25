<?php declare(strict_types=1);

namespace Casa\YouLess\Api;

use Casa\YouLess\Boot\Boot;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

final class App
{
    /** @var RouteCollection */
    protected $_routes;

    private function _handleAction(Request $request, ?callable $action) : Response
    {
        if (!$action) {
            throw new InvalidRequest($request);
        }

        $response = \call_user_func($action, $request);
        if ($response instanceof Response) {
            return $response;
        }

        throw new UnexpectedActionResponse($response);
    }

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

    public function addController(ControllerInterface $controller) : self
    {
        $this->_routes->addCollection($controller->getRouteCollection());

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

            $response = $this->_handleAction($request, $match['_action'] ?? null);
        }
        catch (\Exception $e) {
            $response = new Response((string) $e, Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->headers->set('Content-Type', 'text/plain');
        }

        $response->send();
        exit;
    }
}
