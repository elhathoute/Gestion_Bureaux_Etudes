<?php

include "includes/config.php";
include 'functions.php';

// error_reporting(0);
// $ar = $_POST;

$id = $_POST["id"];

$prenom = $_POST["firstName"];
$nom = $_POST["lastName"];
$email = $_POST["email"];
$phone = $_POST["phone"];
$address = $_POST["address"];


$query = "UPDATE `client_individual` SET `prenom`='$prenom',`nom`='$nom',`email`='$email',`tel`='$phone',`address`='$address' WHERE id='$id'";
$res = mysqli_query($cnx,$query);

if($res){
    // save Record
    $cl_id = $id;
    $user_id = $_SESSION['user_id'];
    userClient_history($user_id,$cl_id,'individual','Update');
    
    $data = array('status'=>'success');
    echo json_encode($data);
}else{
    $data = array('status'=>'failed');
    echo json_encode($data);
}

?>