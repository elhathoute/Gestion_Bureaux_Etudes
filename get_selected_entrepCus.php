<?php include "includes/config.php";

$id = $_POST['id'];

$query = "SELECT * FROM client_entreprise WHERE id='$id'";
$res = mysqli_query($cnx,$query);
$clientData = mysqli_fetch_assoc($res);

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