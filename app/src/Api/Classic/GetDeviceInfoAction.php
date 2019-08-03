<?php declare(strict_types=1);

namespace Casa\YouLess\Api\Classic;

use Casa\YouLess\Api\ActionInterface;
use Casa\YouLess\Device\DeviceFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

class GetDeviceInfoAction implements ActionInterface
{
    public static function createRoute() : Route
    {
        return new Route('/d');
    }

    public function execute(Request $request, Response $response)
    {
        if (!($response instanceof JsonResponse)) {
            return $response->setStatusCode(404);
        }

        $device = DeviceFactory::instance()->get();
        $response->setData([
            'model' => (string) $device->getModel(),
            'mac' => $device->getMac(),
        ]);
    }
}
