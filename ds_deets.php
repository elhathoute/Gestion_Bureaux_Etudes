<?php
include "includes/config.php";

$dDevisId = $_POST["d_devis_id"];

$query = "CALL `sp_getDossierDetail`('".$dDevisId."');";
$res = mysqli_query($cnx,$query);


$data = array();
while($row=mysqli_fetch_assoc($res)){
    
    $data[] = strtoupper($row['ref']);
    $data[] = ucfirst($row['objet']);
    $data[] = ucfirst($row['service_name']);
    $data[] = $row['prix'];
    $data[] = $row["id"];
    
}

echo json_encode($data);


?>