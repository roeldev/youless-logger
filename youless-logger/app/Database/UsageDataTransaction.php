<?php declare(strict_types=1);

namespace Casa\YouLess\Database;

use Casa\YouLess\Database;
use Casa\YouLess\Exceptions\UnknownDelta;
use Casa\YouLess\Response\UsageData;

class UsageDataTransaction
{
    protected $_data;

    public function __construct(UsageData $data)
    {
        $this->_data = $data;
    }

    public function save()
    {
        $unit = \strtolower($this->_data->getUnit());
        $delta = $this->_data->getDelta();

        if (null === $delta) {
            throw UnknownDelta::factory($this->_data->getDeltaTime())->create();
        }

        $pdo = Database::instance();
        $pdo->beginTransaction();

        $values = $this->_data->getValues();
        foreach ($values as $timestamp => $value) {
            $query = $pdo->prepare('
                INSERT OR IGNORE INTO `data`(`timestamp`, `delta`, `unit`, `value`, `date`)
                VALUES(:timestamp, :delta, :unit, :value, :date)'
            );

            $query->execute([
                ':timestamp' => $timestamp,
                ':delta' => $delta,
                ':unit' => $unit,
                ':value' => $value,
                ':date' => date('Y-m-d H:i:s', $timestamp),
            ]);
        }

        $pdo->commit();
    }
}
