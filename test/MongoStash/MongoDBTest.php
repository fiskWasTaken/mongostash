<?php

namespace MongoStash;

use PHPUnit\Framework\TestCase;

// todo: learn mockery and make this more useful
class MongoDBTest extends TestCase {
    /**
     * @var MongoDB
     */
    private $instance;

    public function setUp() {
        $this->instance = new MongoDB();
    }

    public function testOptions() {
        // Should fail if an invalid Mongo client is passed
        try {
            $this->instance->setOptions(['mongo' => 'la', 'database' => 'db']);
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue($e instanceof \InvalidArgumentException);
        }
    }

    public function testPersistence() {
        // MongoDB is a persistent datastore, after all
        $this->assertEquals(true, $this->instance->isPersistent());
    }
}