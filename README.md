# MongoDB Driver for Stash 

A simple MongoDB Driver for Stash. Currently only supports the MongoClient library (mongo-php-driver-legacy). Support for mongo-php-library will be added when that library is more feature-complete and stable.

## Usage

    use MongoStash\MongoClassic;
    use Stash\Pool;
    
    $client = new \MongoClient(); // Whatever it is you are doing to create your Mongo client instance
    $collection = $client->selectDB("db")->selectCollection("stash.store");
    
    $pool = new Pool(new MongoClassic(['collection' => $collection]));
    
And you are ready to go.