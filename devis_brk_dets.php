<?php
include "includes/config.php";

$devis_broker_id = $_POST["dBrk_id"];
$prices = $_POST['prices'];
$res ='';
foreach ($prices as $price) {
    $query = "INSERT INTO `detail_broker_devis`(`id`, `id_broker_devis`, `prix`) VALUES (null,'$devis_broker_id','$price')";
    $res = mysqli_query($cnx,$query);
}

if($res){
    $data = array('status'=>'success');
    echo json_encode($data);
}else{
    $data = array('status'=>'failed');
    echo json_encode($data);
}


?>