<?php
include "includes/config.php";

$devisId = $_POST["devisId"];

$query = "
SELECT detail_devis.service_name,detail_devis.prix,detail_devis.id,detail_devis.quantity,dossier.*,count(dossier.id)
FROM `detail_devis`
INNER JOIN dossier on dossier.id_service=detail_devis.id
WHERE `id_devis`=$devisId AND `confirmed`='1' GROUP by dossier.id_service HAVING COUNT(dossier.id)<detail_devis.quantity;
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
    $subarray[] = $row['id_service'];
    $data[] = $subarray;
    $number++;
}

$output = array('data'=>$data);

echo json_encode($output);


?>