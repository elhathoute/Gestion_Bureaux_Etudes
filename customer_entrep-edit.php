<?php
include "includes/config.php";
include 'functions.php';

$id = $_POST["id"];

$name = $_POST["name"];
$ice = $_POST["ice"];
$email = $_POST["email"];
$phone = $_POST["phone"];
$address = $_POST["address"];


$query = "UPDATE `client_entreprise` SET `nom`='$name',`ICE`='$ice',`email`='$email',`tel`='$phone',`address`='$address' WHERE id='$id'";
$res = mysqli_query($cnx,$query);
if($res){
    // save Record
    $cl_id = $id;
    $user_id = $_SESSION['user_id'];
    userClient_history($user_id,$cl_id,'entreprise','Update');

    $data = array('status'=>'success');
    echo json_encode($data);
}else{
    $data = array('status'=>'failed');
    echo json_encode($data);
}

?>