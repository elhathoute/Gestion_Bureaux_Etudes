<?php include "includes/config.php";

$id = $_POST['id'];

$query = "SELECT * FROM client_entreprise WHERE id='$id'";
$res = mysqli_query($cnx,$query);
$row = mysqli_fetch_assoc($res);
echo json_encode($row);
?>