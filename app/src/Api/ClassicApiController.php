<?php declare(strict_types=1);

namespace Casa\YouLess\Api;

use Casa\YouLess\Device\DeviceFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ClassicApiController implements ControllerInterface
{
    protected function _createRoute(string $path, string $action) : Route
    {
        $route = new Route($path);
        $route->setMethods('GET');
        $route->setDefault('_action', [ $this, $action ]);

        return $route;
    }

    protected function _createRedirect(string $path) : Route
    {
        $route = $this->_createRoute($path . '{query}', 'redirectAction');
        $route->setRequirement('query', '(\?.+)?');

        return $route;
    }

    public function getRouteCollection() : RouteCollection
    {
        $routes = new RouteCollection();
        $routes->add('index', $this->_createRoute('/', 'indexAction'));
        $routes->add('a', $this->_createRedirect('/a'));
        $routes->add('d', $this->_createRedirect('/d'));
        $routes->add('e', $this->_createRedirect('/e'));

        return $routes;
    }

    public function indexAction() : Response
    {
        $endpoints = [
            [ 'Actual data', 'text', '/a' ],
            [ 'Actual data', 'json', '/a?f=j' ],
            [ 'Device info', 'json', '/d' ],
            [ 'e', 'json', '/e' ],
            [ 'Power usage', 'json', '/V?h=1&f=j' ],
            [ 'Gas usage', 'json', '/W?h=1&f=j' ],
            [ 'S0 meter', 'json', '/Z?h=1&f=j' ],
        ];

        $html = [];
        foreach ($endpoints as [$title, $content, $href]) {
            $html[] = <<<HTML
                <tr>
                    <td>${title}</td>
                    <td>${content}</td>
                    <td><a href="${href}">${href}</a></td>
                </tr>
            HTML;
        }

        $html = \implode('', $html);
        $html = <<<HTML
            <table border="1" cellpadding="5" cellspacing="0">
                ${html}
            </table>
        HTML;

        return new Response($html);
    }

    public function redirectAction(Request $request) : Response
    {
        $device = DeviceFactory::instance()
            ->get((string) $request->getPort(), 'port');

        return new Response('', Response::HTTP_TEMPORARY_REDIRECT, [
            'Location' => $device->getHost() . $request->getRequestUri(),
        ]);
    }
}
