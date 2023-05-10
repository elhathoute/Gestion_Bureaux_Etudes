<?php
include "includes/config.php";

$clientId = $_POST["clientID"];

$query = "SELECT `id`, `number`, `id_client`, `date_creation`, `objet` FROM `devis` WHERE `id_client`= '$clientId' AND `remove`=0 AND `client_approve`=0 AND `type`='Approved';";

$res = mysqli_query($cnx,$query);


$data = array();
$number = 1;
while($row=mysqli_fetch_assoc($res)){
    
    $subarray = array();
    $subarray[] = $number;
    $subarray[] = $row['number'];
    $subarray[] = $row['objet'];
    $subarray[] = $row['date_creation'];
    $subarray[] = '<a target="_blank" href="devis_export.php?id='.$row['id'].'&client_id='.$clientId.'" class="btn btn-secondary btn-sm" title="Afficher Devis" ><span><i class="bi bi-eye"></i></span></a>';
    $subarray[] = $row['id'];
    $data[] = $subarray;
    $number++;
}

$output = array('data'=>$data);

echo json_encode($output);


?>