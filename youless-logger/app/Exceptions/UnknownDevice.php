<?php declare(strict_types=1);

namespace Casa\YouLess\Exceptions;

use Stellar\Exceptions\ExceptionFactory;
use Stellar\Exceptions\Logic\OutOfRangeException;
use Stellar\Exceptions\Severity;

final class UnknownDevice extends OutOfRangeException
{
    public static function factory(string $device) : ExceptionFactory
    {
        return ExceptionFactory::init(self::class)
            ->withMessage('Device `{device}` is not configured')
            ->withArguments(\compact('device'))
            ->withSeverity(Severity::WARNING());
    }
}
