<?php

include "includes/config.php";
include 'functions.php';


$id = $_POST["id"];
$title =mysqli_real_escape_string($cnx,$_POST["catTitle"]);
$type = mysqli_real_escape_string($cnx,$_POST['catType']);


$query = "UPDATE `supp_category` SET `title`='$title',`type`='$type' WHERE id=$id";
$res = mysqli_query($cnx,$query);


if($res){
    $data = array('status'=>'success');
    echo json_encode($data);
}else{
    $data = array('status'=>'failed');
    echo json_encode($data);
}

?>