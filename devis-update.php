<?php
include 'includes/config.php';
include 'functions.php';

if ($_POST) {
    $devis_id = $_POST['devis_id'];

    $originalServices = getDevisAllDetails($devis_id);

    $devisStatus = $_POST['devisStatus'];
    $devis_comment = $_POST['devis_comment'];
    $objet_name = $_POST['objet_name'];
    $located = $_POST['located_txt'];
    $tva_checked = isset($_POST['tva_checked']) && $_POST['tva_checked'] == 'true' ? 1 : 0;

    if (isset($_POST['brkId'])) {
        $brkId = $_POST['brkId'];
    }
    $label_subTotal = floatval(trim(str_replace('DH', '', $_POST['labelSubTotal'])));
    $label_discount = floatval(trim(str_replace('DH', '', $_POST['labelDiscount'])));
    $label_netTotal = floatval(trim(str_replace('DH', '', $_POST['labelDevisTotal'])));

    $query = "UPDATE `devis` SET  `sub_total`='$label_subTotal', `discount`='$label_discount', `net_total`='$label_netTotal', `type`='Approved', `status`='accepter', `remove_tva`=$tva_checked, `comment`='$devis_comment', `objet`='$objet_name', `located`='$located' WHERE id='$devis_id'";
    $res = mysqli_query($cnx, $query);

    // Adding to user_devis for history...
    $user_id = $_SESSION['user_id'];
    userDevis_history($user_id, $devis_id, 'Update');

    $tableData = $_POST['tableData'];
    $updatedServices = json_decode($tableData, true);

    // foreach ($originalServices as $index => $originalService) {
    //     if (isset($updatedServices[$index]) && $updatedServices[$index] !== null) {
    //         // Check for updates to the service
    //         $updated_service = $updatedServices[$index];
    //         $discount = $updated_service['discount'] == '' ? 0 : $updated_service['discount'];

    //     //     if (
    //     //         $originalService['service_name'] !== $updated_service['serviceName'] ||
    //     //         $originalService['prix'] !== $updated_service['price'] ||
    //     //         $originalService['quantity'] !== $updated_service['quantity'] ||
    //     //         $originalService['discount'] !== $discount ||
    //     //         $originalService['unit'] !== $updated_service['unit'] ||
    //     //         $originalService['ref'] !== $updated_service['srvRef']
    //     //     ) {
    //     //         $service_id = $originalService['id'];
    //     //         $service_name = $updated_service['serviceName'];
    //     //         $price = $updated_service['price'];
    //     //         $qte = $updated_service['quantity'];
    //     //         $unit = $updated_service['unit'];
    //     //         $ref = $updated_service['srvRef'];

    //     //         $query = "UPDATE `detail_devis` SET `service_name`='$service_name',`prix`='$price',`quantity`='$qte',`discount`='$discount',`unit`='$unit',`ref`='$ref'  WHERE `id` = '$service_id'";
    //     //         mysqli_query($cnx, $query);
    //     //     }
    //     // } 
    //     // else {
    //         // Service has been deleted
    //         // Delete service from the database
    //         $service_id = $originalService['id'];
    //         $query = "DELETE FROM `detail_devis` WHERE `id` = '$service_id'";
    //         mysqli_query($cnx, $query);
    //     // }
    // }
    $query = "DELETE FROM `detail_devis` WHERE `id_devis` = '$devis_id'";
    mysqli_query($cnx, $query);
    $empl = 1;
    foreach ($updatedServices as $index => $updated_service) {
        // if ($updated_service !== null && !isset($originalServices[$index])) {
            for ($i = 0; $i < $updated_service['quantity']; $i++) {
                $service_name = $updated_service['serviceName'];
                $price = $updated_service['price'];
                $qte = $updated_service['quantity'];
                $unit = $updated_service['unit'];
                $ref = $updated_service['srvRef'];
                $serviceUniqueId = $updated_service['serviceUniqueId'];
                $discount = $updated_service['discount'] == '' ? 0 : $updated_service['discount'];

                $query = "INSERT INTO `detail_devis`(`id_devis`, `service_name`, `prix`, `quantity`, `discount`, `unit`, `ref`, `srv_unique_id`, `empl`) VALUES ('$devis_id','$service_name','$price','$qte','$discount','$unit','$ref','$serviceUniqueId','$empl')";
                mysqli_query($cnx, $query);
            }
        // }
        $empl++;
    }

    $dBrk_id = '';
    if (isset($brkId)) {
        $dBrk_id = getBrokerByDevis($devis_id, $brkId);
    }

    if ($res) {
        $data = array('status' => 'success', 'dBrk_id' => $dBrk_id, 'devis_id' => $devis_id);
        echo json_encode($data);
    } else {
        $data = array('status' => 'failed');
        echo json_encode($data);
    }
} else {
    header("location:devis-view.php");
    exit();
}
?>
