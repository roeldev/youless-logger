<?php declare(strict_types=1);

namespace Casa\YouLess\Database;

use Casa\YouLess\Boot\Config;
use PDO;
use Stellar\Common\Contracts\SingletonInterface;
use Stellar\Container\Abilities\SingletonInstanceTrait;

final class Database extends PDO implements SingletonInterface
{
    use SingletonInstanceTrait;

    public function __construct()
    {
        parent::__construct(
            \sprintf('sqlite:%s/data/youless-logger.db', Config::PROJECT_DIR),
            null,
            null, [
                PDO::ATTR_CASE => PDO::CASE_NATURAL,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]
        );
    }
}
