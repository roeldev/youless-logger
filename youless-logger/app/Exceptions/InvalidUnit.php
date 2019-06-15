<?php declare(strict_types=1);

namespace Casa\YouLess\Exceptions;

use Stellar\Exceptions\Logic\OutOfRangeException;
use Throwable;

final class InvalidUnit extends OutOfRangeException
{
    public function __construct(string $unit, ?Throwable $previous = null)
    {
        parent::__construct('Invalid unit `{unit}`', 0, $previous, \compact('unit'));
    }
}
