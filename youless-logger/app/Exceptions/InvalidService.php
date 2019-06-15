<?php declare(strict_types=1);

namespace Casa\YouLess\Exceptions;

use Stellar\Exceptions\Logic\OutOfRangeException;
use Throwable;

final class InvalidService extends OutOfRangeException
{
    public function __construct(string $service, ?Throwable $previous = null)
    {
        parent::__construct('Invalid service `{service}`', 0, $previous, \compact('service'));
    }
}
