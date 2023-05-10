<?php

include "includes/config.php";
include 'functions.php';


$id = $_POST["id"];

$title = $_POST["title"];
$prix = $_POST["prix"];
$ref = $_POST["servRef"];

$query = "UPDATE `service` SET `title`='$title',`ref`='$ref' WHERE id=$id";
$res = mysqli_query($cnx,$query);
// $service_id = $id;
$user_id = $_SESSION['user_id'];
if($res){
    userService_history($user_id,$title,'Update');
    
    $data = array('status'=>'success');
    echo json_encode($data);
}else{
    $data = array('status'=>'failed');
    echo json_encode($data);
}

?>