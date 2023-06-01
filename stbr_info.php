<?php
include "includes/config.php";
$brokerId = $_POST["brokerId"];
$query = "SELECT d.id, d.id_client, d.number, d.remove_tva, dd.ref, dd.service_name, d.date_creation,dd.prix,dd.discount ,
CASE
    WHEN c.type = 'individual' THEN (SELECT CONCAT(ci.prenom, ' ', UPPER(ci.nom)) AS Client FROM client_individual ci WHERE c.id_client = ci.id)
    WHEN c.type = 'entreprise' THEN (SELECT UPPER(ce.nom) FROM client_entreprise ce WHERE c.id_client = ce.id)
END AS client,
d.objet, COALESCE(dp.total_montant_paye, 0) AS total_montant_paye
FROM devis d
INNER JOIN client c ON d.id_client = c.id
INNER JOIN detail_devis dd ON d.id = dd.id_devis
INNER JOIN broker_devis bd ON d.id = bd.id_devis
LEFT JOIN (
    SELECT id_devis,SUM(montant_paye) AS total_montant_paye
    FROM devis_payments
    GROUP BY id_devis
) dp ON dd.id = dp.id_devis
WHERE d.remove = 0 and bd.id_broker =$brokerId AND dd.confirmed=1
ORDER BY d.date_creation;";
$result =mysqli_query($cnx,$query);
$data = array();
$number = 1;
while($row=mysqli_fetch_assoc($result)){
    $prix_t =($row['remove_tva']==1)?$row['prix'] :$row['prix']+($row['prix']*0.2) ;
    $prix =($row['discount']>0)?$prix_t-($prix_t*($row['discount']/100)):$prix_t;
    if($row['total_montant_paye']==0){
        $status='<span class="badge text-bg-danger">Non Payé</span>';
    }elseif($row['total_montant_paye']<$prix && $row['total_montant_paye'] != 0){
        $status='<span class="badge avance-color">Avance</span>';
    }else{
        $status= '<span class="badge text-bg-success">Payé</span>';
    }
    if($row['prix']==0){
        $status = '<span class="badge text-bg-warning">Gratuit</span>';
    }
    $subarray = array();
    $subarray[] = $number;
    $subarray[] = $row['number'];
    $subarray[] = $row['objet'];
    $subarray[] = $row['service_name'];
    $subarray[] = $prix;
    $subarray[] = $row['total_montant_paye'];
    $subarray[] = $status;
    $subarray[] = '<a target="_blank" href="devis_export.php?id='.$row['id'].'&client_id='.$row['id_client'].'" class="btn btn-secondary btn-sm" title="Afficher Devis" ><span><i class="bi bi-eye"></i></span></a>';
    $data[] = $subarray;
    $number++;
}

$output = array('data'=>$data);

echo json_encode($output);

?>