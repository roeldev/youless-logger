<?php declare(strict_types=1);

namespace Casa\YouLess\Exceptions;

use Stellar\Exceptions\Logic\OutOfRangeException;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

final class InvalidRequest extends OutOfRangeException
{
    public function __construct(Request $request, ?Throwable $previous = null)
    {
        $request = (string) $request;
        parent::__construct('Invalid request `{request}`', 0, $previous, \compact('request'));
    }
}
