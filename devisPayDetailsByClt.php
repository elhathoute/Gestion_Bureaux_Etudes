<?php
include "includes/config.php";
include "./functions.php";

$clientId = $_POST["clientId"];
$query = "CALL `sp_getDevisPayByClient`('".$clientId."');";
$res = mysqli_query($cnx, $query);
$data = array();
while ($row = mysqli_fetch_assoc($res)) {
    // $avance =floatval($row['total_montant_paye']);
    $avance =$row['total_montant_paye'];
    $subarray = array();
    $subarray[] = $row['number'];
    $subarray[] = $row['client'];
    $subarray[] = ($row['N_dossier'] != NULL) ? $row['N_dossier'] : '-';
    $subarray[] = $row['service_name'];
    // $subarray[] = 'Qte=' . $row['quantity'] . ' count_dossier=' . getCountDossierService($row['srv_id']);
    $subarray[] =$row['quantity'] ;
    $subarray[] = $row['discount']== 0?$row['srv_prix']:$row['srv_prix']-(($row['srv_prix']*$row['discount'])/100);
    $subarray[] = $row['total_montant_paye']== NULL?'0.00 ':$avance;
    $subarray[] = '<input type="checkbox" name="ids[]" class="CBPaymentByClient" value="'.$row["srv_id"].'"><input type="checkbox" name="devis[]" class="DevisCheckBox d-none " value="'.$row["id"].'"><input type="checkbox" name="dossiers[]" class="DossierCheckBox d-none" value="'.$row["id_dossier"].'"><input type="hidden" name="Dossiers[]" class="" value="'.$row["id_dossier"].'"><input type="hidden" name="servicesId[]" class="" value="'.$row["srv_id"].'"><input type="hidden" name="uniqueIds[]" class="" value="'.$row["srv_unique_id"].'"><input type="checkbox" name="checkeduniqueIds[]" class="serviceUICheckBox d-none" value="'.$row["srv_unique_id"].'">';
    $data[] = $subarray;
}

$output = array('data' => $data);
echo json_encode($output);
?>
