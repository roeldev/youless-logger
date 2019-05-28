<?php declare(strict_types=1);

namespace Casa\YouLess\Exceptions;

use Stellar\Exceptions\ExceptionFactory;
use Stellar\Exceptions\Logic\InvalidArgumentException;

class UnknownDelta extends InvalidArgumentException
{
    /**
     * @return ExceptionFactory
     */
    public static function factory(int $deltaTime) : ExceptionFactory
    {
        return ExceptionFactory::init(self::class)
            ->withMessage('Unknown delta time encountered `{deltaTime}`')
            ->withArgument('deltaTime', $deltaTime);
    }
}
