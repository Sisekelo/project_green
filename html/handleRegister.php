<?php

require '../vendor/autoload.php';

$client = new MongoDB\Client("mongodb://127.0.0.1:27017");


 if(isset($_POST['submit'])){
      
    $farmId = "F".rand(1000,9999);
    $farmName = ($_POST['farmName']);
    $farmerNumber = ($_POST['farmerNumber']);
    $farmRegion = ($_POST['farmRegion']);
    $farmNotes = ($_POST['farmNotes']);
      
    

    $db = $client->farm;
    $collection = $db->$farmId;
     
     
     $document = array( 
          "farmId" => $farmId, 
          "farmName" => $farmName, 
          "farmerNumber" => $farmerNumber,
          "farmRegion" => $farmRegion,
          "farmNotes" => $farmNotes,
          "farmDeliveries" => "None"
       );
     
     $collection->insertOne($document);

     $message = "Farm ID: ".$farmId." \n \n Please write down farm ID";
     

    echo $message;
}

elseif(isset($_POST['submitDeliver'])){

  $farmId = ($_POST['farmIdDelivery']);

  $db = $client->farm;
  $collection = $db->$farmId;

  $cursor = $collection->find();

  foreach ($cursor as $document) {

      $farmName = $document["farmName"];
      $farmerNumber = $document["farmerNumber"];
      $farmRegion = $document["farmRegion"];
      $farmNotes = $document["farmNotes"];

     };

     $parameters = "name=".$farmName."&farmerNumber=".$farmerNumber."&farmRegion=".$farmRegion."&farmNotes=".$farmNotes."&farmId=".$farmId;

     //go to site
     header("Location: deliverInfo.html?".$parameters);
     exit;
}

elseif(isset($_POST['submitDeliverInfo'])){

  $deliveriesInfo
   = ($_POST['deliveriesInfo']);

   $deliveriesInfoId
   = ($_POST['farmIdName']);

  $db = $client->farm;
  $collection = $db->$deliveriesInfoId;

  $collection->updateOne(array("farmDeliveries"=>"None"), 
      array('$set'=>array("farmDeliveries"=>$deliveriesInfo)));

     //go to site
     header("Location: deliver.html");
     exit;
}
     
else{
    echo "Please go to the previous page";
}
?>