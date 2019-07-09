<?php declare(strict_types=1);

namespace Casa\YouLess\Exceptions;

use Stellar\Exceptions\Logic\OutOfRangeException;
use Throwable;

final class InvalidInterval extends OutOfRangeException
{
    public function __construct(string $interval, ?Throwable $previous = null)
    {
        parent::__construct('Invalid interval `{interval}`', 0, $previous, \compact('interval'));
    }
}
