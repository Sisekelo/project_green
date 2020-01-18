<?php
$client = new MongoDB\Client("mongodb://127.0.0.1:27017");


//farm information only
$db = $client->farm;
$farmIdObjects = $db->listCollections();

    foreach ($farmIdObjects as $farmIdObject)
    {

        $farmId = $farmIdObject->getName();

        $collection = $db->$farmId;

        $cursor = $collection->find();

        $items[] = iterator_to_array($collection->find());
    }

    echo "<script>localStorage.farmInformation = JSON.stringify(" . json_encode($items) . ")</script>";

//product information
$db2 = $client->products;
$collection2 = $db2->foods;

$foodItems[] = iterator_to_array($collection2->find());

echo "<script>localStorage.productInformation = JSON.stringify(" . json_encode($foodItems) . ")</script>";


?>

