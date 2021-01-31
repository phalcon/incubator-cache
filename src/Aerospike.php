<?php

namespace Phalcon\Incubator\Cache;

use Phalcon\Cache\Adapter\AdapterInterface as CacheAdapterInterface;
use Phalcon\Cache\Exception\Exception;
use Phalcon\Helper\Arr;
use Phalcon\Storage\Adapter\AbstractAdapter;
use Phalcon\Storage\SerializerFactory;

/**
 * Aerospike adapter
 */
class Aerospike extends AbstractAdapter implements CacheAdapterInterface
{
    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $set = 'cache';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var bool
     */
    protected $persistent = false;

    /**
     * Constructor
     *
     * @param SerializerFactory $factory
     * @param array $options = [
     *
     * ]
     */
    public function __construct(SerializerFactory $factory, array $options = [])
    {
        $options['hosts'] = Arr::get($options, 'hosts', ['127.0.0.1']);

        if (Arr::has($options, 'namespace')) {
            $this->namespace = Arr::get($options, 'namespace');

            unset($options['namespace']);
        }

        if (Arr::has($options, 'namespace')) {
            $this->set = Arr::get($options, 'set');

            unset($options['set']);
        }

        if (Arr::has($options, 'persistent')) {
            $this->persistent = (bool)Arr::get($options, 'persistent');
        }

        parent::__construct($factory, $options);
    }

    public function clear(): bool
    {
        $success = true;
        $keys = $this->getKeys();

        foreach ($keys as $aKey) {
            if (!$this->delete($aKey)) {
                $success = false;
            }
        }

        return $success;
    }

    public function decrement(string $key, int $value = 1)
    {
        $prefixedKey = $this->getPrefixedKey($key);
        $aKey = $this->buildKey($prefixedKey);
        $this->getAdapter()->increment($aKey, 'value', -1 * abs($value));

        $status = $this->getAdapter()->get($aKey, $cache);

        if ($status != \Aerospike::OK) {
            return false;
        }

        return $cache['bins']['value'];
    }

    public function delete(string $key): bool
    {
        $prefixedKey = $this->getPrefixedKey($key);
        $aKey = $this->buildKey($prefixedKey);

        $status = $this->getAdapter()->remove($aKey);

        return $status == \Aerospike::OK;
    }

    public function get(string $key, $defaultValue = null)
    {
        $prefixedKey = $this->getPrefixedKey($key);
        $aKey = $this->buildKey($prefixedKey);

        $status = $this->getAdapter()->get($aKey, $cache);

        if ($status != \Aerospike::OK) {
            return null;
        }

        return $cache['bins']['value'];
    }

    /**
     * @return \Aerospike
     */
    public function getAdapter()
    {
        if (null === $this->adapter) {
            $connection = new \Aerospike(
                [
                    'hosts' => $this->options['hosts'],
                ],
                $this->persistent,
                $this->options
            );

            $this->adapter = $connection;
        }

        return $this->adapter;
    }

    public function getKeys(string $prefix = ''): array
    {
        if (!$prefix) {
            $prefix = $this->prefix;
        } else {
            $prefix = $this->getPrefixedKey($prefix);
        }

        $keys = [];
        $globalPrefix = $this->prefix;

        $this->getAdapter()->scan(
            $this->namespace,
            $this->set,
            function ($record) use (&$keys, $prefix, $globalPrefix) {
                $key = $record['key']['key'];

                if (empty($prefix) || 0 === strpos($key, $prefix)) {
                    $keys[] = preg_replace(
                        sprintf(
                            '#^%s(.+)#u',
                            preg_quote($globalPrefix)
                        ),
                        '$1',
                        $key
                    );
                }
            }
        );

        return $keys;
    }

    public function has(string $key): bool
    {
        $prefixedKey = $this->getPrefixedKey($key);
        $aKey = $this->buildKey($prefixedKey);

        return $this->getAdapter()->exists($aKey, $cache) == \Aerospike::OK;
    }

    public function increment(string $key, int $value = 1)
    {
        $prefixedKey = $this->getPrefixedKey($key);
        $aKey = $this->buildKey($prefixedKey);
        $this->getAdapter()->increment($aKey, 'value', $value);

        $status = $this->getAdapter()->get($aKey, $cache);

        if ($status != \Aerospike::OK) {
            return false;
        }

        return $cache['bins']['value'];
    }

    public function set(string $key, $value, $ttl = null): bool
    {
        $prefixedKey = $this->getPrefixedKey($key);
        $content = $this->getSerializedData($value);
        $ttl = $this->getTtl($ttl);
        $aKey = $this->buildKey($prefixedKey);
        $bins['value'] = $content;

        $status = $this->getAdapter()->put(
            $aKey,
            $bins,
            $ttl,
            [
                \Aerospike::OPT_POLICY_KEY => \Aerospike::POLICY_KEY_SEND,
            ]
        );

        if (\Aerospike::OK != $status) {
            throw new Exception(
                sprintf(
                    'Failed storing data in Aerospike: %s',
                    $this->getAdapter()->error()
                ),
                $this->getAdapter()->errorno()
            );
        }

        return \Aerospike::OK == $status;
    }

    /**
     * Generates a unique key used for storing cache data in Aerospike DB.
     *
     * @param string $key Cache key
     * @return array
     */
    protected function buildKey(string $key): array
    {
        return $this->getAdapter()->initKey(
            $this->namespace,
            $this->set,
            $key
        );
    }
}