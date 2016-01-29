<?php

namespace MongoStash;

use Stash\Driver\AbstractDriver;
use Stash\Exception\InvalidArgumentException;


/**
 * Supports the classic Mongo PHP driver, the MongoClient class. Stores persistent cache to MongoDB, which can be a
 * good option for a distributed cache.
 *
 * @package MongoStash
 */
class MongoDB extends AbstractDriver {
    /**
     * @var \MongoCollection|\MongoDB\Collection
     */
    private $collection;

    /**
     * @param array $key
     * @return string
     */
    private static function mapKey($key)
    {
        return implode('/', $key);
    }

    /**
     * {@inheritdoc}
     */
    public function getData($key)
    {
        $doc = $this->collection->findOne(['_id' => self::mapKey($key)]);
        return $doc ? ['data' => unserialize($doc['data']), 'expiration' => $doc['expiration']] : false;
    }

    /**
     * {@inheritdoc}
     */
    public function storeData($key, $data, $expiration)
    {
        if ($this->collection instanceof \MongoDB\Collection) {
            $this->collection->insertOne(['_id' => self::mapKey($key), 'data' => serialize($data), 'expiration' => $expiration]);
        } else {
            $this->collection->save(['_id' => self::mapKey($key), 'data' => serialize($data), 'expiration' => $expiration]);
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clear($key = null)
    {
        if (!$key) {
            $this->collection->drop();
            return true;
        }

        if ($this->collection instanceof \MongoDB\Collection) {
            $this->collection->deleteMany(['_id' => new \MongoDB\BSON\Regex("^" . preg_quote(self::mapKey($key)))]);
        } else {
            $this->collection->remove(['_id' => new \MongoRegex("^" . preg_quote(self::mapKey($key)))], ['multiple' => true]);
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function purge()
    {
        if ($this->collection instanceof \MongoDB\Collection) {
            $this->collection->deleteMany(['expiration' => ['$lte' => time()]]);
        } else {
            $this->collection->remove(['expiration' => ['$lte' => time()]], ['multiple' => true]);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions()
    {
        return array(
            'mongo' => null,
            'database' => null,
            'collection' => 'stash.store'
        );
    }

    /**
     * collection - A MongoCollection instance. Required.
     *
     * @param array $options
     * @throws InvalidArgumentException
     */
    public function setOptions(array $options = array())
    {
        $options += $this->getDefaultOptions();

        if (!($options['mongo'] instanceof \MongoClient || $options['mongo'] instanceof \Mongo || $options['mongo'] instanceof \MongoDB\Client)) {
            throw new \InvalidArgumentException('MongoClient, Mongo or MongoDB\Client instance required');
        }

        $this->collection = $options['mongo']->selectCollection($options['database'], $options['collection']);
    }

    /**
     * {@inheritdoc}
     */
    public static function isAvailable()
    {
        return class_exists('\MongoDB\Client', false) || class_exists('\MongoClient', false);
    }

    /**
     * {@inheritdoc}
     */
    public function isPersistent()
    {
        return true;
    }
}