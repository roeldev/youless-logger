<?php declare(strict_types=1);

namespace Casa\YouLess\Exceptions;

use Stellar\Exceptions\ExceptionFactory;
use Stellar\Exceptions\Runtime\RuntimeException;

class EmptyEnv extends RuntimeException
{
    /**
     * @return ExceptionFactory
     */
    public static function factory(string $envVar) : ExceptionFactory
    {
        return ExceptionFactory::init(self::class)
            ->withMessage('Missing or empty environment variable `{envVar}`')
            ->withArgument('envVar', $envVar);
    }
}
