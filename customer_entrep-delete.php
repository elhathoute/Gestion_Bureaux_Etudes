<?php
include "includes/config.php";
include 'functions.php';

$id = $_POST["id"];
$query = "UPDATE `client_entreprise` SET `delete_status`='1' WHERE id= '$id'";
$res = mysqli_query($cnx,$query);
if($res){
    // save Record
    $cl_id = $id;
    $user_id = $_SESSION['user_id'];
    userClient_history($user_id,$cl_id,'entreprise','Delete');

    $data = array('status'=>'success');
    echo json_encode($data);
}else{
    $data = array('status'=>'failed');
    echo json_encode($data);
}



?>