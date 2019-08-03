<?php declare(strict_types=1);

namespace Casa\YouLess\Database;

use PDOStatement;

interface UpdatableRecordInterface
{
    public function isDirty() : bool;

    public function createInsertStatement() : PDOStatement;

    public function createUpdateStatement() : PDOStatement;

    public function save() : void;
}
