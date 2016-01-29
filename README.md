# MongoDB Driver for Stash 

A simple MongoDB Driver for Stash, supporting MongoClient and the new-style MongoDB library.

## Usage

    $mongo = new \MongoClient(); // Whatever it is you are doing to create your Mongo client instance
    
    $pool = new \Stash\Pool(new \MongoStash\MongoDB([
        'mongo' => $mongo,
        'database' => 'local',
        'collection' => 'stash.store'
    ]));
    
And you are ready to go.