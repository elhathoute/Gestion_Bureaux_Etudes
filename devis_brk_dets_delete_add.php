<?php
include "includes/config.php";

if ($_POST) {
 
    $devis_broker_id = $_POST["dBrk_id"];
    $devis_id = $_POST["devis_id"];
    $prices = $_POST['prices'];
    // die(var_dump($devis_broker_id));
    $BrkSubTotal = $_POST['BrkSubTotal'];
    $BrkDiscount = $_POST['BrkDiscount'];
    $BrkDevisTotal = $_POST['BrkDevisTotal'];
    $request="UPDATE `broker_devis` SET `discount`='$BrkDiscount',`sub_total`='$BrkSubTotal',`net_total`='$BrkDevisTotal' WHERE `id_devis`='$devis_id'";
    $result=mysqli_query($cnx, $request);
    // Delete existing services
    $query1 = "DELETE FROM detail_broker_devis WHERE id_broker_devis = $devis_broker_id";
    $res1 = mysqli_query($cnx, $query1);
    // die(var_dump($res1));
    if ($res1) {
    $res='';
        // Insert new services
        foreach ($prices as $price) {
            $_price = $price["price"];
            $_discount = $price["discount"];
            $_service_unique_id = $price["service_unique_id"];
            $query = "INSERT INTO `detail_broker_devis`(`id`, `id_broker_devis`, `new_prix`, `srv_unique_id`, `new_discount`) VALUES (null, '".$devis_broker_id."', '".$_price."', '".$_service_unique_id."', '".$_discount."')";

            $res = mysqli_query($cnx, $query);
        }
        // die(var_dump($res));
        if ($res) {
            $data = array('status' => 'success');
            echo json_encode($data);
        } else {
            $data = array('status' => 'failed');
            echo json_encode($data);
        }
    } 
    else {
        $data = array('status' => 'failed');
        echo json_encode($data);
    }
}
?>
