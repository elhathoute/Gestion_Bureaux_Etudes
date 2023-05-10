<?php
include "includes/config.php";
include 'functions.php';

$id = $_POST["id"];
$title = getServiceById($id)['title'];
$query = "DELETE FROM `service` WHERE id= '$id'";
$res = mysqli_query($cnx,$query);
// $service_id = $id;
if($res){
    $user_id = $_SESSION['user_id'];
    userService_history($user_id,$title,'Delete');
    
    $data = array('status'=>'success');
    echo json_encode($data);
}else{
    $data = array('status'=>'failed');
    echo json_encode($data);
}



?>