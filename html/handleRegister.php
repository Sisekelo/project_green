<?php
require '../vendor/autoload.php';


include "SimpleXLSX.php";
//require "backgroundProcesses.php";

$client = new MongoDB\Client("mongodb://127.0.0.1:27017");
$db = $client->farm;

//create a farm
if (isset($_POST['submit']))
{

    $farmId = "F" . rand(1000, 9999);
    $farmName = ($_POST['farmName']);
    $farmerNumber = ($_POST['farmerNumber']);
    $farmRegion = ($_POST['farmRegion']);
    $farmNotes = ($_POST['farmNotes']);

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

    $message = "Farm registered. Farm ID: " . $farmId . ".";

    $url = "deliver.html?id=" . $farmId;

    echo "<script type='text/javascript'>alert('$message');document.location='$url'</script>";
}

//search for farm
elseif (isset($_POST['submitDeliver']))
{

    $farmId = ($_POST['farmIdDelivery']);

    $db = $client->farm;
    $collection = $db->$farmId;

    $cursor = $collection->find();

    foreach ($cursor as $document)
    {

        $farmName = $document["farmName"];
        $farmerNumber = $document["farmerNumber"];
        $farmRegion = $document["farmRegion"];
        $farmNotes = $document["farmNotes"];
        $farmerDeliveries = $document['farmDeliveries'];

    };

    $parameters = "name=" . $farmName . "&farmerNumber=" . $farmerNumber . "&farmRegion=" . $farmRegion . "&farmNotes=" . $farmNotes . "&farmId=" . $farmId . "&farmDeliveries=" . $farmerDeliveries;

    $url = "deliverInfo.html?" . $parameters;

    //go to site
    header("Location: " . $url);
    exit;
}

//update a farm
elseif (isset($_POST['submitDeliverInfo']))
{

    $deliveriesInfo = ($_POST['deliveriesInfo']);

    $deliveriesInfoId = ($_POST['farmIdName']);

    $pastDeliveryInfo = ($_POST['pastFarmDeliveriesInfo']);

    $farmId = ($_POST['farmIdHidden']);

    $combinedDeliveries = $pastDeliveryInfo . "<br> <br>" . $deliveriesInfo;

    $db = $client->farm;
    $collection = $db->$deliveriesInfoId;

    $collection->updateOne(array(
        "farmDeliveries" => $pastDeliveryInfo
    ) , array(
        '$set' => array(
            "farmDeliveries" => $combinedDeliveries
        )
    ));

    //go to site
    header("Location: deliver.html?id=" . $farmId);
    exit;
}

//search in the navigation bar
elseif (isset($_POST['navSearch']))
{

    $inputFarmName = ($_POST['navSearchInput']);

    $farmIdObjects = $db->listCollections();

    foreach ($farmIdObjects as $farmIdObject)
    {

        $farmId = $farmIdObject->getName();

        $collection = $db->$farmId;

        $cursor = $collection->find();

        foreach ($cursor as $document)
        {

            $farmId = $document["farmId"];
            $farmName = $document["farmName"];
            $farmerNumber = $document["farmerNumber"];
            $farmRegion = $document["farmRegion"];
            $farmNotes = $document["farmNotes"];
            $farmerDeliveries = $document['farmDeliveries'];

            if ($inputFarmName == $farmName)
            {

                $parameters = "name=" . $farmName . "&farmerNumber=" . $farmerNumber . "&farmRegion=" . $farmRegion . "&farmNotes=" . $farmNotes . "&farmId=" . $farmId . "&farmDeliveries=" . $farmerDeliveries;

                $url = "deliverInfo.html?" . $parameters;

                //go to site
                header("Location: " . $url);
                exit;

            }

        };

    }

    $message = "The farm name: $inputFarmName, does not seem to exist";

    $url = "register.html";

    echo "<script type='text/javascript'>alert('$message');document.location='$url'</script>";

}

elseif (isset($_POST['navStatistics']))
{

   $farmIdObjects = $db->listCollections();

    foreach ($farmIdObjects as $farmIdObject)
    {

        $farmId = $farmIdObject->getName();

        $collection = $db->$farmId;

        $cursor = $collection->find();

        $items[] = iterator_to_array($collection->find());
    }

    echo "<script>console.log(" . json_encode($items) . " );</script>";
    echo "<script>localStorage.farmInformation = JSON.stringify(" . json_encode($items) . ")</script>";

    $url = "stats.html";

    echo "<script>document.location='$url'</script>";

}

elseif (isset($_FILES['docUploaded']))
{

    $db = $client->products;
    $collection = $db->foods;

    $file = $_FILES['docUploaded']['tmp_name'];

    if ($xlsx = SimpleXLSX::parse($file))
    {
        echo 'we got here!';
        $i = 0;

        $stringHeader0;
        $stringHeader1;
        $stringHeader2;
        $stringHeader3;

        foreach ($xlsx->rows() as $elt)
        {
            if ($i == 0)
            {

                for ($x = 0;$x <= 3;$x++)
                {
                    $elt[$x] = preg_replace("/[^A-Za-z0-9 ]/", '', strtolower($elt[$x]));

                }

                $stringHeader0 = $elt[0];
                
                echo $stringHeader0;
                
                $stringHeader1 = $elt[1];
                $stringHeader2 = $elt[2];
                $stringHeader3 = $elt[3];

            }
            else
            {

                $document = array(

                    $stringHeader0 => $elt[0],
                    $stringHeader1 => $elt[1],
                    $stringHeader2 => $elt[2],
                    $stringHeader3 => $elt[3]

                );

                $collection->insertOne($document);
            }
            $i++;
        }
    }
    else
    {
        echo SimpleXLSX::parseError();
    }
    
   $foodItems[] = iterator_to_array($collection->find());

    echo "<script>localStorage.productInformation = JSON.stringify(" . json_encode($foodItems) . ")</script>";

    $url = "price.html";

    echo "<script>document.location='$url'</script>";
}

elseif (isset($_POST['navPrices'])){
    
    $db = $client->products;
    $collection = $db->foods;
    
    $foodItems[] = iterator_to_array($collection->find());

    echo "<script>localStorage.productInformation = JSON.stringify(" . json_encode($foodItems) . ")</script>";
    
    $url = "price.html";

    echo "<script>document.location='$url'</script>";
}

else
{
    echo "Please go to the previous page";
}
?>
