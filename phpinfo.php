<?php

require 'vendor/autoload.php';



/*$client = new MongoDB\Client(
    'mongodb+srv://sisekelo:sisekelo@webparktrial-n0itw.mongodb.net/test?retryWrites=true&w=majority');*/
   

/*$client = new MongoDB\Client("mongodb+srv://sisekelo:sisekelo@webparktrial-n0itw.mongodb.net/test?retryWrites=true&w=majority");*/

$client = new MongoDB\Client("mongodb://127.0.0.1:27017");

$item = "F6269";

$db = $client->farm;
$collection = $db->$item;

$cursor = $collection->find();

foreach ($cursor as $document) {
      echo $document["farmId"] . "\n";
      echo $document["farmRegion"] . "\n";
   };
	
?>

<!-- 
<?php

require 'vendor/autoload.php';



$client = new MongoDB\Client(
    'mongodb+srv://sisekelo:sisekelo@webparktrial-n0itw.mongodb.net/test?retryWrites=true&w=majority');
   

/*$client = new MongoDB\Client("mongodb+srv://sisekelo:sisekelo@webparktrial-n0itw.mongodb.net/test?retryWrites=true&w=majority");*/

/*$client = new MongoDB\Client("mongodb://127.0.0.1:27017");*/

$companydb = $client->companydb44;
$result1 = $companydb->createCollection('empcollection2');
var_dump($result1);

	
?> -->

<!-- <?


$client = new MongoDB\Client(
    'mongodb+srv://sisekelo:sisekelo@webparktrial-n0itw.mongodb.net/test?retryWrites=true&w=majority');

$db = $client->test;


?> -->


