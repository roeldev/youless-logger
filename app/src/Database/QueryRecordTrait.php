<?php declare(strict_types=1);

namespace Casa\YouLess\Database;

trait QueryRecordTrait
{
    protected function queryRecord(string $statement, ...$args) : array
    {
        $query = Database::instance()->prepare($statement);
        $query->execute($args);

        return $query->fetch(\PDO::FETCH_ASSOC) ?: [];
    }
}
