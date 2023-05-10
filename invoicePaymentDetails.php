<?php
include "includes/config.php";

$clientId = $_POST["clientId"];
$query = "CALL `sp_getInvPayByClient`('".$clientId."');";
$res = mysqli_query($cnx,$query);
$data = array();
while($row=mysqli_fetch_assoc($res)){
    $subarray = array();
    $subarray[] = $row['F_number'];
    $subarray[] = $row['client'];
    $subarray[] = $row['objet'];
    $subarray[] = $row['date_creation'];
    $subarray[] = $row['net_total'];
    $subarray[] = $row['solde'];
    $subarray[] = '<input type="checkbox" name="ids[]" class="CBPaymentByClient" value="'.$row["id"].'">';
    $subarray[] = '<input type="hidden" name="invoiceId[]" class="" value="'.$row["id"].'">';
    $data[] = $subarray;
}

$output = array('data'=>$data);

echo json_encode($output);


?>