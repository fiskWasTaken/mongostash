# MongoDB Driver for Stash 

This is a MongoDB Driver for [Stash](https://github.com/tedious/Stash), supporting MongoClient and the new-style MongoDB library.

This enables you to use a persistent and distributed store as your cache driver, which can be ideal for homogenic and distributed systems.

## Usage

    $mongo = new \MongoClient(); // Whatever it is you are doing to create your Mongo client instance
    
    $pool = new \Stash\Pool(new \MongoStash\MongoDB([
        'mongo' => $mongo,
        'database' => 'local',
        'collection' => 'stash.store' // This is optional, stash.store is the default.
    ]));
    
And you are ready to go.