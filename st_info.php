<?php
include "includes/config.php";



$clientId = $_POST["clientID"];
$query = "CALL `sp_getDevisSituation`('".$clientId."');";
$res = mysqli_query($cnx,$query);
$data = array();
$number = 1;
while($row=mysqli_fetch_assoc($res)){
    $subarray = array();
    $subarray[] = $number;
    $subarray[] = $row['number'];
    $subarray[] = $row['objet'];
    $subarray[] = $row['service_name'];
    $subarray[] = $row['prix'];
    $subarray[] = $row['avance'];
    $subarray[] = $row['paid_srv'];
    $subarray[] = '<a target="_blank" href="devis_export.php?id='.$row['id'].'&client_id='.$clientId.'" class="btn btn-secondary btn-sm" title="Afficher Devis" ><span><i class="bi bi-eye"></i></span></a>';
    $subarray[] = $row['remove_tva'];
    $data[] = $subarray;
    $number++;
}

$output = array('data'=>$data);

echo json_encode($output);














// $clientId = $_POST["clientID"];
// $query = "CALL `sp_getSituation`('".$clientId."');";
// $res = mysqli_query($cnx,$query);
// $data = array();
// $number = 1;
// while($row=mysqli_fetch_assoc($res)){
//     $subarray = array();
//     $subarray[] = $number;
//     $subarray[] = $row['F_number'];
//     $subarray[] = $row['objet'];
//     $subarray[] = $row['net_total'];
//     $subarray[] = $row['avance'];
//     $subarray[] = $row['paid_inv'];
//     $subarray[] = '<a target="_blank" href="invoice_export.php?id='.$row['id'].'&client_id='.$clientId.'" class="btn btn-secondary btn-sm" title="Afficher Facture" ><span><i class="bi bi-eye"></i></span></a>';
//     $data[] = $subarray;
//     $number++;
// }

// $output = array('data'=>$data);

// echo json_encode($output);


?>