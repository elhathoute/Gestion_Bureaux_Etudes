<?php

include "includes/config.php";
include 'functions.php';


$id = $_POST["id"];

$full_name = mysqli_real_escape_string($cnx,$_POST['full_name']);
$phone = mysqli_real_escape_string($cnx,$_POST['phone']);
$address = mysqli_real_escape_string($cnx,$_POST['address']);
$cat_id  = $_POST['cat_id'];


$query = "UPDATE `supplier` SET `full_name`='$full_name',`address`='$address',`phone`='$phone',`cat_id`='$cat_id' WHERE id=$id";
$res = mysqli_query($cnx,$query);


if($res){
    $category = getSuppCatById($cat_id)['title'];
    $sold = getSupplierById($id)['sold'];
    $data = array('status'=>'success','category'=>$category,'sold'=> $sold);
    echo json_encode($data);
}else{
    $data = array('status'=>'failed');
    echo json_encode($data);
}

?>