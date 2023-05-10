<?php
include "includes/config.php";

$broker_id = $_POST["broker_id"];
$query = "CALL `sp_getDevisPayByBroker`('".$broker_id."');";
$res = mysqli_query($cnx,$query);
$data = array();
while($row=mysqli_fetch_assoc($res)){
    $subarray = array();
    $subarray[] = $row['number'];
    $subarray[] = $row['client'];
    $subarray[] = $row['ref'];
    $subarray[] = $row['service_name'];
    $subarray[] = $row['srv_prix'];
    $subarray[] = $row['solde'];
    $subarray[] = '<input type="checkbox" name="ids[]" class="CBPaymentByClient" value="'.$row["srv_id"].'">';
    $subarray[] = '<input type="hidden" name="invoiceId[]" class="" value="'.$row["srv_id"].'">';
    $subarray[] = $row['client_id'];
    $data[] = $subarray;
}

$output = array('data'=>$data);

echo json_encode($output);


?>