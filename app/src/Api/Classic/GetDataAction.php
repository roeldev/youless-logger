<?php declare(strict_types=1);

namespace Casa\YouLess\Api\Classic;

use Casa\YouLess\Api\ActionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

class GetDataAction implements ActionInterface
{
    public static function createRoute() : Route
    {
        return new Route('/V');
    }

    public function execute(Request $request, Response $response) : Response
    {
        return $response->setContent('test');
    }
}
