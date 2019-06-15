<?php declare(strict_types=1);

namespace Casa\YouLess\Exceptions;

use Stellar\Exceptions\Runtime\RuntimeException;
use Throwable;

final class UnsupportedService extends RuntimeException
{
    public function __construct(string $service, string $model, ?Throwable $previous = null)
    {
        parent::__construct(
            'Model `{model}` does not support service `{service}`',
            0,
            $previous,
            \compact('service', 'model')
        );
    }
}
