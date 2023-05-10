<?php
include "includes/config.php";

$id = $_POST["id"];
$query = "DELETE FROM `users` WHERE `id`='$id'";
$res = mysqli_query($cnx,$query);
if($res){
    $data = array('status'=>'success');
    echo json_encode($data);
}else{
    $data = array('status'=>'failed');
    echo json_encode($data);
}


?>