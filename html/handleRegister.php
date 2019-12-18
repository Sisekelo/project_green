<?php

require '../vendor/autoload.php';


 if(isset($_POST['submit'])){
      
    $farmId = "F".rand(1000,9999);
    $farmName = ($_POST['farmName']);
    $farmerNumber = ($_POST['farmerNumber']);
    $farmRegion = ($_POST['farmRegion']);
    $farmNotes = ($_POST['farmNotes']);
      
    $client = new MongoDB\Client("mongodb://127.0.0.1:27017");

    $db = $client->farm;
    $collection = $db->$farmId;
     
     
     $document = array( 
          "farmId" => $farmId, 
          "farmName" => $farmName, 
          "farmerNumber" => $farmerNumber,
          "farmRegion" => $farmRegion,
          "farmNotes" => $farmNotes
       );
     
     $collection->insertOne($document);

     $message = "Farm ID: ".$farmId." \n \n Please write down farm ID";
     

    echo $message;

elseif($_GET['submit'])){

	echo $_GET['farmIdDelivery'];

}
     
else{
    echo "Please go to the previous page";
}
?>