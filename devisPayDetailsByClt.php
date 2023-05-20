<?php
include "includes/config.php";
include "./functions.php";

$clientId = $_POST["clientId"];
$query = "CALL `sp_getDevisPayByClient`('".$clientId."');";
$res = mysqli_query($cnx,$query);
$data = array();
// die(json_encode(mysqli_fetch_assoc($res)));
while($row=mysqli_fetch_assoc($res)){
    for($i=0;$i< $row['quantity'];$i++){

    $subarray = array();
    $subarray[] = $row['number'];
    $subarray[] = $row['client'];
    
    $subarray[] =($row['N_dossier']!=NULL) ? $row['N_dossier'] : '-';
    $subarray[] = $row['service_name'];
    // $subarray[] = $row['quantity'];
    $subarray[] ='Qte='.$row['quantity'] .'count_dossier= '.getCountService($row['srv_id']);

    $subarray[] = $row['srv_prix'];
    $subarray[] = $row['solde'];
    $subarray[] = '<input type="checkbox" name="ids[]" class="CBPaymentByClient" value="'.$row["srv_id"].'">';
    $subarray[] = '<input type="hidden" name="invoiceId[]" class="" value="'.$row["srv_id"].'">';
    $data[] = $subarray;
}

}

$output = array('data'=>$data);

echo json_encode($output);


?>