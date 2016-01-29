# MongoDB Driver for Stash 

A simple MongoDB Driver for Stash, supporting MongoClient and the new-style MongoDB library.

## Usage

    use MongoStash\MongoClassic;
    use Stash\Pool;
    
    $client = new \MongoClient(); // Whatever it is you are doing to create your Mongo client instance
    $collection = $client->selectDB("db")->selectCollection("stash.store");
    
    $pool = new Pool(new MongoClassic(['collection' => $collection]));
    
And you are ready to go.