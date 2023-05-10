<?php

include "includes/config.php";
include 'functions.php';


$id = $_POST["id"];
$brkNom = $_POST["brkNom"];
$brkPrenom = $_POST["brkPrenom"];
$brkPhone = $_POST["brkPhone"];
$brkAdr = $_POST["brkAdr"];


$query = "UPDATE `broker` SET `nom`='$brkNom',`prenom`='$brkPrenom',`phone`='$brkPhone',`address`='$brkAdr' WHERE id=$id";
$res = mysqli_query($cnx,$query);

$user_id = $_SESSION['user_id'];
$fullBrokerName = $brkNom .' '. $brkPrenom;
if($res){
    userBroker_history($user_id,$fullBrokerName,'Update');
    
    $data = array('status'=>'success');
    echo json_encode($data);
}else{
    $data = array('status'=>'failed');
    echo json_encode($data);
}

?>