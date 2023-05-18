<?php
include "includes/config.php";

$devis_broker_id = $_POST["dBrk_id"];
$devis_id = $_POST["devis_id"];
$prices = $_POST['prices'];
// $discounts = $_POST['discounts'];
// $service_unique_ids = $_POST['service_unique_ids'];
$res ='';
foreach ($prices as $price) {
    // die(var_dump($price));
    $_price=$price["price"];
    $_discount=$price["discount"];
    $_service_unique_id=$price["service_unique_id"];
    // die(var_dump('pr'.$price['price'].'disc'.$price['discount'].'srv_unique'.$price['service_unique_id']));
    $query = "INSERT INTO `detail_broker_devis`(`id`, `id_broker_devis`, `new_prix`,`srv_unique_id`,`new_discount`) VALUES (null,'$devis_broker_id',$_price,$_service_unique_id,$_discount)";

    // $query = "INSERT INTO `detail_broker_devis`(`id`, `id_broker_devis`, `new_prix`,`srv_unique_id`,`new_discount`) VALUES (null,'$devis_broker_id',$_price,$_service_unique_id,$_discount)";
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