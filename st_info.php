<?php
include "includes/config.php";
$clientId = $_POST["clientID"];
$query = "CALL `sp_getDevisSituation`('".$clientId."');";
$result =mysqli_query($cnx,$query);
$data = array();
$number = 1;
while($row=mysqli_fetch_assoc($result)){
    $prix_t =($row['remove_tva']==1)?$row['prix'] :$row['prix']+($row['prix']*0.2) ;
    $prix2 =($row['discount']>0)?$prix_t-($prix_t*($row['discount']/100)):$prix_t;
    // $prix = number_format($prix2,2);
    $prix = sprintf("%.2f", $prix2);
    if($row['total_montant_paye']==0){
        $status='<span class="badge text-bg-danger">Non Payé</span>';
        $statusValue="Non Payé";
    }elseif($row['total_montant_paye']<$prix && $row['total_montant_paye'] != 0){
        $status='<span class="badge avance-color">Avance</span>';
        $statusValue="Avance";
    }else{
        $status= '<span class="badge text-bg-success">Payé</span>';
        $statusValue="Payé";
    }
    if($row['prix']==0){
        $status = '<span class="badge text-bg-warning">Gratuit</span>';
        $statusValue="Gratuit";
    }
    $subarray = array();
    $subarray[] = $number;
    $subarray[] = $row['number'];
    $subarray[] = $row['objet'];
    $subarray[] = $row['service_name'];
    $subarray[] = $prix2;
    $subarray[] = $row['total_montant_paye'];
    $subarray[] = $status;
    $subarray[] = '<a target="_blank" href="devis_export.php?id='.$row['id'].'&client_id='.$clientId.'" class="btn btn-secondary btn-sm" title="Afficher Devis" ><span><i class="bi bi-eye"></i></span></a>';
    $subarray[] = $row['date_creation'];
    $subarray[] = $row['devis_date'];
    $subarray[] = $statusValue;
    $data[] = $subarray;
    $number++;
}
$output = array('data'=>$data);
echo json_encode($output);
?>