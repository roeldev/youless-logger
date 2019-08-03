<?php declare(strict_types=1);

namespace Casa\YouLess\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

interface ActionInterface
{
    public static function createRoute() : Route;

    public function execute(Request $request, Response $response);
}
