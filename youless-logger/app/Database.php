<?php declare(strict_types=1);

namespace Casa\YouLess;

use PDO;
use Stellar\Common\Contracts\SingletonInterface;
use Stellar\Container\Abilities\SingletonInstanceTrait;

final class Database extends PDO implements SingletonInterface
{
    use SingletonInstanceTrait;

    public function __construct()
    {
        parent::__construct('sqlite:/youless-logger/data/youless-logger.db', null, null, [
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }
}
