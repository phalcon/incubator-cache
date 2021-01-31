<?php

namespace Phalcon\Incubator\Cache;

use Phalcon\Cache\Adapter\AdapterInterface as CacheAdapterInterface;
use Phalcon\Cache\Exception\Exception;
use Phalcon\Db\Adapter\AdapterInterface as DbAdapterInterface;
use Phalcon\Db\Enum as DbEnum;
use Phalcon\Helper\Arr;
use Phalcon\Storage\Adapter\AbstractAdapter;
use Phalcon\Storage\SerializerFactory;

/**
 * Database adapter
 */
class Database extends AbstractAdapter implements CacheAdapterInterface
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Constructor
     *
     * @param SerializerFactory $factory
     * @param array $options = [
     *
     * ]
     * @throws Exception
     */
    public function __construct(SerializerFactory $factory, array $options = [])
    {
        if (!Arr::has($options, 'db') || !Arr::get($options, 'db') instanceof DbAdapterInterface) {
            throw new Exception(
                'Parameter "db" is required and it must be an instance of Phalcon\Db\Adapter\AdapterInterface'
            );
        }

        if (!Arr::has($options, 'table') && !is_string(Arr::get($options, 'table'))) {
            throw new Exception("Parameter 'table' is required and it must be a non empty string");
        }

        $this->adapter = Arr::get($options, 'db');
        $this->table = $this->getAdapter()->escapeIdentifier(
            Arr::get($options, 'table')
        );

        unset(
            $options['db'],
            $options['table']
        );

        parent::__construct($factory, $options);
    }

    public function clear(): bool
    {
        $this->getAdapter()->execute("DELETE FROM {$this->table}");

        return true;
    }

    public function decrement(string $key, int $value = 1)
    {
        if (!$this->has($key)) {
            return false;
        }

        $data = $this->get($key);
        $data = (int)$data - $value;

        return $this->set($key, $data);
    }

    public function delete(string $key): bool
    {
        $prefixedKey = $this->getPrefixedKey($key);
        $sql = "SELECT COUNT(*) AS rowcount FROM {$this->table} WHERE key_name = ?";

        $row = $this->getAdapter()->fetchOne(
            $sql,
            DbEnum::FETCH_ASSOC,
            [
                $prefixedKey,
            ]
        );

        if (!Arr::has($row, 'rowcount')) {
            return false;
        }

        return $this->getAdapter()->execute(
            "DELETE FROM {$this->table} WHERE key_name = ?",
            [
                $prefixedKey,
            ]
        );
    }

    public function get(string $key, $defaultValue = null)
    {
        $prefixedKey = $this->getPrefixedKey($key);
        $sql = "SELECT data, lifetime FROM {$this->table} WHERE key_name = ?";

        $cache = $this->getAdapter()->fetchOne(
            $sql,
            DbEnum::FETCH_ASSOC,
            [
                $prefixedKey,
            ]
        );

        if (!$cache) {
            return null;
        }

        // Remove the cache if expired
        if ($cache['lifetime'] < time()) {
            $this->getAdapter()->execute(
                "DELETE FROM {$this->table} WHERE key_name = ?",
                [
                    $prefixedKey,
                ]
            );

            return null;
        }

        return $this->getUnserializedData(
            $cache['data']
        );
    }

    /**
     * @return DbAdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    public function getKeys(string $prefix = ''): array
    {
        if (!$prefix) {
            $prefix = $this->prefix;
        } else {
            $prefix = $this->getPrefixedKey($prefix);
        }

        if (!empty($prefix)) {
            $sql = "SELECT key_name FROM {$this->table} WHERE key_name LIKE ? ORDER BY lifetime";
            $rs = $this->getAdapter()->query(
                $sql,
                [
                    $prefix . '%',
                ]
            );
        } else {
            $sql = "SELECT key_name FROM {$this->table} ORDER BY lifetime";
            $rs = $this->getAdapter()->query($sql);
        }

        $keys = [];
        $rs->setFetchMode(DbEnum::FETCH_ASSOC);

        while ($row = $rs->fetch()) {
            $keys[] = !empty($prefix) ? str_replace($prefix, '', $row['key_name']) : $row['key_name'];
        }

        return $keys;
    }

    public function has(string $key): bool
    {
        $prefixedKey = $this->getPrefixedKey($key);
        $sql = "SELECT lifetime FROM {$this->table} WHERE key_name = ?";

        $cache = $this->getAdapter()->fetchOne(
            $sql,
            DbEnum::FETCH_ASSOC,
            [
                $prefixedKey,
            ]
        );

        if (!$cache) {
            return false;
        }

        // Remove the cache if expired
        if (Arr::get($cache, 'lifetime') < time()) {
            $this->getAdapter()->execute(
                "DELETE FROM {$this->table} WHERE key_name = ?",
                [
                    $prefixedKey,
                ]
            );

            return false;
        }

        return true;
    }

    public function increment(string $key, int $value = 1)
    {
        if (!$this->has($key)) {
            return false;
        }

        $data = $this->get($key);
        $data = (int)$data + $value;

        return $this->set($key, $data);
    }

    public function set(string $key, $value, $ttl = null): bool
    {
        $prefixedKey = $this->getPrefixedKey($key);
        $sql = "SELECT data, lifetime FROM {$this->table} WHERE key_name = ?";

        $cache = $this->getAdapter()->fetchOne(
            $sql,
            DbEnum::FETCH_ASSOC,
            [
                $prefixedKey,
            ]
        );

        if (!$cache) {
            $status = $this->getAdapter()->execute(
                "INSERT INTO {$this->table} VALUES (?, ?, ?)",
                [
                    $prefixedKey,
                    $this->getSerializedData($value),
                    $this->getTtl($ttl),
                ]
            );
        } else {
            $status = $this->getAdapter()->execute(
                "UPDATE {$this->table} SET data = ?, lifetime = ? WHERE key_name = ?",
                [
                    $this->getSerializedData($value),
                    $this->getTtl($ttl),
                    $prefixedKey
                ]
            );
        }

        return $status;
    }
}
