<?php include "includes/config.php";

$id = $_POST['id'];

$query = "SELECT * FROM `supplier` WHERE id='$id'";
$res = mysqli_query($cnx,$query);
$row = mysqli_fetch_assoc($res);
echo json_encode($row);
?>