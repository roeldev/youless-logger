<?php declare(strict_types=1);

namespace Casa\YouLess\Exceptions;

use Stellar\Exceptions\Logic\OutOfRangeException;
use Throwable;

final class UnknownDevice extends OutOfRangeException
{
    public function __construct(string $device, ?Throwable $previous = null)
    {
        parent::__construct('Device `{device}` is not configured', 0, $previous, \compact('device'));
    }
}
