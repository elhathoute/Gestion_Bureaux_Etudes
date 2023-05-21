<?php
include "includes/config.php";

$devisId = $_POST["devisId"];

$query = "SELECT detail_devis.service_name, detail_devis.id AS service_id, detail_devis.prix, detail_devis.quantity, dossier.*, COUNT(dossier.id)
FROM detail_devis
LEFT JOIN dossier ON dossier.id_service = detail_devis.id
WHERE detail_devis.id_devis = $devisId AND detail_devis.confirmed = '1'
GROUP BY dossier.id_service
HAVING COUNT(dossier.id) < detail_devis.quantity;
;
";
$res = mysqli_query($cnx,$query);


$data = array();
$number = 1;
while($row=mysqli_fetch_assoc($res)){
    
    $subarray = array();
    $subarray[] = ucfirst($row['service_name']);
    $subarray[] = $row['prix'];
    // $subarray[] = '<button class="dsServiceItem btn btn-primary"><i class="bi bi-plus-circle"></i><button>';
    $subarray[] = '<span><i class="bi bi-plus-circle"></i></span>';
    $subarray[] = $row['service_id'];
    // die(print_r($row));
    $data[] = $subarray;
    $number++;
}

$output = array('data'=>$data);

echo json_encode($output);


?>