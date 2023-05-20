<?php
include "includes/config.php";

$devis_broker_id = $_POST["dBrk_id"];
$devis_id = $_POST["devis_id"];
$prices = $_POST['prices'];

$res = '';
foreach ($prices as $price) {
    $_price = $price["price"];
    $_discount = $price["discount"];
    $_service_unique_id = $price["service_unique_id"];

    $query = "INSERT INTO `detail_broker_devis`(`id`, `id_broker_devis`, `new_prix`, `srv_unique_id`, `new_discount`) VALUES (null, '$devis_broker_id', '$_price', '$_service_unique_id', '$_discount')";

    $res = mysqli_query($cnx, $query);
}

if ($res) {
    $data = array('status' => 'success');
    echo json_encode($data);
} else {
    $data = array('status' => 'failed');
    echo json_encode($data);
}
?>
