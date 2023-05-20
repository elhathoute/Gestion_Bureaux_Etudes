<?php
include "includes/config.php";
include "./functions.php";

$broker_id = $_POST["broker_id"];
$query = "CALL `sp_getDevisPayByBroker`('".$broker_id."');";
$res = mysqli_query($cnx,$query);
$data = array();
$data_2 = array();

while ($row = mysqli_fetch_assoc($res)) {
     if($row['quantity']==getCountDossierService($row['srv_id'])){

   
    $subarray = array();
    $subarray[] = $row['number'];
    $subarray[] = $row['client'];
    $subarray[] = ($row['N_dossier'] != NULL) ? $row['N_dossier'] : '-';
    $subarray[] = $row['service_name'];
    $subarray[] = 'Qte=' . $row['quantity'] . ' count_dossier=' . getCountDossierService($row['srv_id']);
    $subarray[] = $row['srv_prix'];
    $subarray[] = $row['solde'];
    $subarray[] = '<input type="checkbox" name="ids[]" class="CBPaymentByClient" value="'.$row["srv_id"].'">';
    $subarray[] = '<input type="hidden" name="invoiceId[]" class="" value="'.$row["srv_id"].'">';
    $data[] = $subarray;
}
    else if(getCountDossierService($row['srv_id'])!=0 ){
        
    $subarray = array();
    $subarray[] = $row['number'];
    $subarray[] = $row['client'];
    $subarray[] = ($row['N_dossier'] != NULL) ? $row['N_dossier'] : '-';
    $subarray[] = $row['service_name'];
    $subarray[] = 'Qte=' . $row['quantity'] . ' count_dossier=' . getCountDossierService($row['srv_id']);
    $subarray[] = $row['srv_prix'];
    $subarray[] = $row['solde'];
    $subarray[] = '<input type="checkbox" name="ids[]" class="CBPaymentByClient" value="'.$row["srv_id"].'">';
    $subarray[] = '<input type="hidden" name="invoiceId[]" class="" value="'.$row["srv_id"].'">';
    $data[] = $subarray;

    $diff_count_qte=$row['quantity'] - getCountDossierService($row['srv_id']);
    for ($j = 0; $j <$diff_count_qte ; $j++) {
        $subarray2 = array();
        $subarray2[] = $row['number'];
        $subarray2[] = $row['client'];
        $subarray2[] = '-';
        $subarray2[] = $row['service_name'];
        $subarray2[] = 'Qte=' . $row['quantity'] . ' count_dossier=' . getCountDossierService($row['srv_id']);
        $subarray2[] = $row['srv_prix'];
        $subarray2[] = $row['solde'];
        $subarray2[] = '<input type="checkbox" name="ids[]" class="CBPaymentByClient" value="'.$row["srv_id"].'">';
        $subarray2[] = '<input type="hidden" name="invoiceId[]" class="" value="'.$row["srv_id"].'">';
        $data_2[] = $subarray2;
        
        
    }
}else{
    for ($j = 0; $j <$row['quantity'] ; $j++) {
    
    $subarray = array();
        $subarray[] = $row['number'];
        $subarray[] = $row['client'];
        
        $subarray[] =($row['N_dossier']!=NULL) ? $row['N_dossier'] : '-';
        $subarray[] = $row['service_name'];
        // $subarray[] = $row['quantity'];
        $subarray[] ='Qte='.$row['quantity'] .'count_dossier= '.getCountDossierService($row['srv_id']);
    
        $subarray[] = $row['srv_prix'];
        $subarray[] = $row['solde'];
        $subarray[] = '<input type="checkbox" name="ids[]" class="CBPaymentByClient" value="'.$row["srv_id"].'">';
        $subarray[] = '<input type="hidden" name="invoiceId[]" class="" value="'.$row["srv_id"].'">';
        $data[] = $subarray;
}
}
}
if(count($data_2)>0){
    
    $data_3 = array_merge($data, array_slice($data_2, 0, $diff_count_qte));
    }
else{
    $data_3 =$data;
}

$output = array('data' => $data_3);
echo json_encode($output);


?>