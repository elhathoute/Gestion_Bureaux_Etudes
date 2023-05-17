<?php
include "includes/config.php";

$id = $_POST['id'];

$query = "SELECT * FROM client_individual WHERE id='$id'";
$res = mysqli_query($cnx, $query);
$clientData = mysqli_fetch_assoc($res);

// Retrieve broker data
$queryBroker = "SELECT * FROM `broker`";
$resBroker = mysqli_query($cnx, $queryBroker);
$brokers = array();
while ($brokerRow = mysqli_fetch_assoc($resBroker)) {
  $brokers[] = $brokerRow;
}

$response = array(
  'customer' => $clientData,
  'brokers' => $brokers
);



echo json_encode($response);
?>
