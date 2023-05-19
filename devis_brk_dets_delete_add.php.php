<?php
include "includes/config.php";

if ($_POST) {
    $devis_broker_id = $_POST["dBrk_id"];
    $devis_id = $_POST["devis_id"];
    $prices = $_POST['prices'];
// die(var_dump($devis_broker_id));
   
    // Delete existing services
    $query1 = "DELETE FROM detail_broker_devis WHERE id_broker_devis = $devis_broker_id";
    $res1 = mysqli_query($cnx, $query1);

    if ($res1) {
        // Insert new services
        $res = '';
        foreach ($prices as $price) {
            $_price = $price["price"];
            $_discount = $price["discount"];
            $_service_unique_id = $price["service_unique_id"];

            $query = "INSERT INTO `detail_broker_devis`(`id`, `id_broker_devis`, `new_prix`, `srv_unique_id`, `new_discount`) VALUES (null, '$devis_broker_id', $_price, $_service_unique_id, $_discount)";
            $res = mysqli_query($cnx, $query);
        }

        if ($res) {
            $data = array('status' => 'success');
            echo json_encode($data);
        } else {
            $data = array('status' => 'failed');
            echo json_encode($data);
        }
    } else {
        $data = array('status' => 'failed');
        echo json_encode($data);
    }
}
?>
