<?php
include "includes/config.php";
include 'functions.php';

$id = $_POST["id"];
$query = "UPDATE `invoice` SET `remove`='1' WHERE id= '$id'";
$res = mysqli_query($cnx,$query);
if($res){
    //adding to user_invoice for history...
    $user_id = $_SESSION['user_id'];
    userInvoice_history($user_id,$id,'Delete');

    $data = array('status'=>'success');
    echo json_encode($data);
}else{
    $data = array('status'=>'failed');
    echo json_encode($data);
}



?>