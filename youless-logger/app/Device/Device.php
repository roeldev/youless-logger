<?php declare(strict_types=1);

namespace Casa\YouLess\Device;

use Casa\YouLess\Contracts\UpdatableRecordInterface;
use Casa\YouLess\Database;
use Casa\YouLess\Device\Models\ModelInterface;
use Stellar\Common\Abilities\StringableTrait;
use Stellar\Common\Contracts\ArrayableInterface;
use Stellar\Common\Contracts\StringableInterface;
use Stellar\Common\StringUtil;
use Stellar\Curl\Curl;
use Stellar\Curl\Request\Request;
use Stellar\Curl\Response\JsonResponse;
use Stellar\Exceptions\Common\MissingArgument;
use PDOStatement;

class Device implements ArrayableInterface, StringableInterface, UpdatableRecordInterface
{
    use StringableTrait;

    /** @var ?int */
    protected $_id;

    /** @var string */
    protected $_name;

    /** @var string */
    protected $_host;

    /** @var string */
    protected $_ip;

    /** @var string */
    protected $_password;

    /** @var string */
    protected $_mac;

    /** @var array */
    protected $_record;

    /** @var ModelInterface */
    protected $_model;

    /** @var array<string, array<string, int>> */
    protected $_services = [];

    protected function _determineActiveServices(array $services) : array
    {
        $result = [];

        $validServices = $this->_model->getServices();
        foreach ($validServices as $service) {
            if (true === ($services[ $service ] ?? false) || \in_array($service, $services, true)) {
                $result[] = $service;
            }
        }

        return $result;
    }

    public function __construct(ModelInterface $model, string $name, array $config, array $record = [])
    {
        $ip = $config['ip'] ?? null;
        if (empty($ip) || !\is_string($ip)) {
            throw new MissingArgument('__construct', static::class);
        }

        $this->_id = isset($record['id']) ? (int) $record['id'] : null;
        $this->_name = $name;
        $this->_host = \sprintf('http://%s/', $ip);
        $this->_ip = $ip;
        $this->_password = $config['password'] ?? null;
        $this->_record = $record;

        $this->_model = $model;
        if (!isset($config['services'])) {
            $this->_services = $model->getServices();
        }
        else {
            $this->_services = $this->_determineActiveServices($config['services']);
        }
    }

    public function getId() : ?int
    {
        return $this->_id;
    }

    public function getName() : string
    {
        return $this->_name;
    }

    public function getHost() : string
    {
        return $this->_host;
    }

    public function getIp() : ?string
    {
        return $this->_ip;
    }

    public function getModel() : ModelInterface
    {
        return $this->_model;
    }

    public function getMac() : ?string
    {
        if (!$this->_mac) {
            /** @var JsonResponse $response */
            $response = $this->createRequest('/d')->response(JsonResponse::class);
            $response = $response->toArray();

            $this->_mac = $response['mac'] ?? null;
        }

        return $this->_mac;
    }

    public function getActiveServices() : array
    {
        return $this->_services;
    }

    public function hasActiveService(string $service) : bool
    {
        return \in_array($service, $this->_services, true);
    }

    public function isDirty() : bool
    {
        return empty($this->_id)
               || ($this->_record['ip'] ?? null) !== $this->_ip
               || ($this->_record['name'] ?? null) !== $this->_name;
    }

    public function createRequest(string $path) : Request
    {
        return Curl::get($this->getHost() . StringUtil::unprefix($path, '/'))
            ->throwExceptionOnFailure()
            ->withQueryParam('f', 'j');
    }

    public function createInsertStatement() : PDOStatement
    {
        $statement = Database::instance()
            ->prepare('INSERT INTO `devices` (`ip`, `name`, `created_at`) 
                        VALUES (:ip, :name, :created_at)');

        $statement->bindValue('ip', $this->_ip);
        $statement->bindValue('name', $this->_name);
        $statement->bindValue('created_at', \time());

        return $statement;
    }

    public function createUpdateStatement() : PDOStatement
    {
        $statement = Database::instance()
            ->prepare('UPDATE `devices` 
                        SET `ip` = :ip, `name` = :name, `updated_at` = :updated_at 
                        WHERE `id` = :id ');

        $statement->bindValue('id', $this->_id);
        $statement->bindValue('ip', $this->_ip);
        $statement->bindValue('name', $this->_name);
        $statement->bindValue('updated_at', \time());

        return $statement;
    }

    public function save() : void
    {
        $createUpdateStatement = empty($this->_id) ? $this->createInsertStatement() : $this->createUpdateStatement();
        $createUpdateStatement->execute();

        $syncStatement = Database::instance()
            ->prepare('SELECT * FROM `devices` WHERE `id` = ?');

        $syncStatement->execute([ $this->_id ]);
        $this->_record = $syncStatement->fetch(\PDO::FETCH_ASSOC);
    }

    public function toArray() : array
    {
        return [
            'name' => $this->_name,
            'model' => (string) $this->_model,
            'host' => $this->_host,
            'ip' => $this->_ip,
            'mac' => $this->getMac(),
        ];
    }

    public function __toString() : string
    {
        return $this->_name;
    }
}
