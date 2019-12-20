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
          "farmDeliveries" => ""
       );
     
     $collection->insertOne($document);

     $message = "Farm ID: ".$farmId.". PLEASE WRTIE DOWN THIS ID";

     $url = "deliver.html?id=".$farmId;

      echo "<script type='text/javascript'>alert('$message');document.location='$url'</script>";
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
      $farmerDeliveries = $document['farmDeliveries'];

     };

     $parameters = "name=".$farmName."&farmerNumber=".$farmerNumber."&farmRegion=".$farmRegion."&farmNotes=".$farmNotes."&farmId=".$farmId."&farmDeliveries=".$farmerDeliveries;

     $url = "deliverInfo.html?".$parameters;


     //go to site

     header("Location: ".$url);
     exit;
}

elseif(isset($_POST['submitDeliverInfo'])){

  $deliveriesInfo
   = ($_POST['deliveriesInfo']);

   $deliveriesInfoId
   = ($_POST['farmIdName']);

   $pastDeliveryInfo
   = ($_POST['pastFarmDeliveriesInfo']);

   $farmId
   = ($_POST[' farmIdHidden']);

   $combinedDeliveries = $pastDeliveryInfo."<br> <br>".$deliveriesInfo;

  $db = $client->farm;
  $collection = $db->$deliveriesInfoId;

  

  $collection->updateOne(array("farmDeliveries"=>$pastDeliveryInfo), 
      array('$set'=>array("farmDeliveries"=>$combinedDeliveries)));

     //go to site
     header("Location: deliver.html?id=".$farmId);
     exit;
}
     
else{
    echo "Please go to the previous page";
}
?>