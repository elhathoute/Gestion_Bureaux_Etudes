<?php include "includes/config.php";

$id = $_POST['devis_id'];
$client_id =$_POST['client_id'];
$query = "SELECT * FROM `detail_devis` WHERE `id_devis`='$id'";
$res = mysqli_query($cnx,$query);
$row = mysqli_fetch_all($res);
// echo json_encode($row);

//**************************************NOT WORKING*************************************
?>