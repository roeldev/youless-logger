<?php declare(strict_types=1);

namespace Casa\YouLess\Api\Classic;

use Casa\YouLess\Api\ControllerInterface;
use Casa\YouLess\Device\DevicesContainer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;

class ApiController implements ControllerInterface
{
    protected function _validateRequest(Request $request) : ?Response
    {
        if (empty($request->query->get('f'))) {
            return new Response('<h1>Format not supported</h1>', Response::HTTP_NOT_IMPLEMENTED);
        }

        if (empty($request->query->get('h'))) {
            return new Response('<h1>404 Niet Gevonden</h1>', Response::HTTP_NOT_FOUND);
        }

        return null;
    }

    public function getRouteCollection() : RouteCollection
    {
        $factory = RouteFactory::instance();

        $routes = new RouteCollection();
        $routes->add('index', $factory->create('/', [ $this, 'indexAction' ]));
        $routes->add('a', $factory->createRedirect('/a', [ $this, 'redirectAction' ]));
        $routes->add('d', $factory->createRedirect('/d', [ $this, 'redirectAction' ]));
        $routes->add('e', $factory->createRedirect('/e', [ $this, 'redirectAction' ]));
        $routes->add('V', $factory->create('V', [ $this, 'powerUsageAction' ], true));

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
        $html = "<table>${html}</table>";

        return new Response($html);
    }

    public function redirectAction(Request $request) : Response
    {
        $device = DevicesContainer::instance()
            ->get((string) $request->getPort(), 'port');

        return new Response('', Response::HTTP_TEMPORARY_REDIRECT, [
            'Location' => $device->getHost() . $request->getRequestUri(),
        ]);
    }

    public function powerUsageAction(Request $request) : Response
    {
        $response = $this->_validateRequest($request);
        if ($response) {
            return $response;
        }

        return new JsonResponse([
            'un' => 'Watt',
            'tm' => '2019-08-26T21:52:00',
            'dt' => 60,
            'val' => [],
        ]);
    }
}
