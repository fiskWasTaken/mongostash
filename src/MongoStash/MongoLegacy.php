<?php

namespace MongoStash;

use Stash\Driver\AbstractDriver;
use Stash\Exception\InvalidArgumentException;


/**
 * Supports the classic Mongo PHP driver; the MongoClient class. Stores persistent cache to MongoDb, which can be a
 * good option for a distributed cache.
 *
 * @package MongoStash
 */
class MongoLegacy extends AbstractDriver {
    /**
     * @var \MongoCollection
     */
    private $__collection;

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
        $doc = $this->__collection->findOne(['_id' => self::mapKey($key)]);

        return $doc ? $doc['data'] : false;
    }

    /**
     * {@inheritdoc}
     */
    public function storeData($key, $data, $expiration)
    {
        $this->__collection->save(['_id' => self::mapKey($key), 'data' => $data, 'expiration' => $expiration]);
    }

    /**
     * {@inheritdoc}
     */
    public function clear($key = null)
    {
        if (!$key)
            return $this->purge();

        $this->__collection->remove(['_id' => new \MongoRegex("^" . preg_quote(self::mapKey($key)))]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function purge()
    {
        $this->__collection->remove([], ['multiple' => true]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions()
    {
        return array(
            'client' => null,
            'collection' => 'stash.store'
        );
    }

    /**
     * client - a MongoClient instance. Required.
     * collection - a collection to use for the Stash store. Default: stash.store
     *
     * @param array $options
     * @throws InvalidArgumentException
     */
    public function setOptions(array $options = array())
    {
        $options += $this->getDefaultOptions();

        if (!$options['client'] instanceof \MongoClient)
            throw new InvalidArgumentException("client is a required parameter and must be an instance of MongoClient.");

        if (!is_string($options['collection']))
            throw new InvalidArgumentException("The collection parameter must be a string if it is set.");

        $this->__collection = $options['client']->selectCollection($options['collection']);
    }

    /**
     * {@inheritdoc}
     */
    public static function isAvailable()
    {
        return class_exists('\MongoClient', false);
    }

    /**
     * {@inheritdoc}
     */
    public function isPersistent()
    {
        return true;
    }
}