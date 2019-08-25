<?php declare(strict_types=1);

namespace Casa\YouLess\Exceptions;

use Stellar\Common\Stringify;
use Stellar\Exceptions\Runtime\RuntimeException;
use Throwable;

final class UnexpectedActionResponse extends RuntimeException
{
    public function __construct($response, callable $action, ?Throwable $previous = null)
    {
        $response = Stringify::any($response);
        $action = Stringify::any($action);

        parent::__construct(
            'Unexpected response `{response}` from action `{action}`',
            0,
            $previous,
            \compact('response', 'action')
        );
    }
}
