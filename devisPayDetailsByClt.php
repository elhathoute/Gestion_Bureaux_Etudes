<?php
include "includes/config.php";
include "./functions.php";

$clientId = $_POST["clientId"];
$query = "CALL `sp_getDevisPayByClient`('".$clientId."');";
$res = mysqli_query($cnx, $query);
$data = array();
while ($row = mysqli_fetch_assoc($res)) {
    $avance =floatval($row['total_montant_paye']);
    $subarray = array();
    $subarray[] = $row['number'];
    $subarray[] = $row['client'];
    $subarray[] = ($row['N_dossier'] != NULL) ? $row['N_dossier'] : '-';
    $subarray[] = $row['service_name'];
    // $subarray[] = 'Qte=' . $row['quantity'] . ' count_dossier=' . getCountDossierService($row['srv_id']);
    $subarray[] =$row['quantity'] ;
    $subarray[] = $row['srv_prix'];
    $subarray[] = $row['total_montant_paye']== NULL?'0.00 ':$avance;
    $subarray[] = '<input type="checkbox" name="ids[]" class="CBPaymentByClient" value="'.$row["srv_id"].'"><input type="checkbox" name="devis[]" class="DevisCheckBox d-none " value="'.$row["id"].'"><input type="checkbox" name="dossiers[]" class="DossierCheckBox d-none" value="'.$row["id_dossier"].'"><input type="hidden" name="invoiceId[]" class="" value="'.$row["srv_id"].'">';
    $data[] = $subarray;
}

$output = array('data' => $data);
echo json_encode($output);
?>
