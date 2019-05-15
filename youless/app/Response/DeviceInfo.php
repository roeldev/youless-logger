<?php declare(strict_types=1);

namespace Casa\YouLess\Response;

use Stellar\Curl\Response\JsonResponse;

class DeviceInfo extends JsonResponse
{
    public function __construct($requestResource,
                                array $usedOptions,
                                string $response)
    {
        parent::__construct($requestResource, $usedOptions, $response);

        $this->_data['ip'] = str_replace('http://', '', \gethostbyname(\getenv('YOULESS_HOST')));
    }
}
