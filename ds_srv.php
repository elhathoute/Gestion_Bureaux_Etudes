<?php
include "includes/config.php";

$devisId = $_POST["devisId"];

$query = "SELECT `id`,`service_name`,`prix` FROM `detail_devis` WHERE `id_devis`='$devisId' AND `confirmed`='1'";
$res = mysqli_query($cnx,$query);


$data = array();
$number = 1;
while($row=mysqli_fetch_assoc($res)){
    
    $subarray = array();
    $subarray[] = ucfirst($row['service_name']);
    $subarray[] = $row['prix'];
    $subarray[] = $row['id'];
    $data[] = $subarray;
    $number++;
}

$output = array('data'=>$data);

echo json_encode($output);


?>