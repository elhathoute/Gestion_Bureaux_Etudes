<?php

include "includes/config.php";
include 'functions.php';

$user_id = $_SESSION['user_id'];
if(isset($_POST['montantPaye'])){
    $id_broker=$_POST["id"];
    $MontantPaye=$_POST['montantPaye'];
    $query = "UPDATE `broker` SET `sold` = (`sold` - $MontantPaye) WHERE `id`='$id_broker'";
    $res = mysqli_query($cnx,$query);
    if($res){
        $data = array('status'=>'success');
        echo json_encode($data);
    }else{
        $data = array('status'=>'failed');
        echo json_encode($data);
    }
}else{
    $id = $_POST["id"];
    $brkNom = mysqli_real_escape_string($cnx,$_POST["brkNom"]);
    $brkPrenom = mysqli_real_escape_string($cnx,$_POST["brkPrenom"]);
    $brkPhone = mysqli_real_escape_string($cnx,$_POST["brkPhone"]);
    $brkAdr = mysqli_real_escape_string($cnx,$_POST["brkAdr"]);
    $brokerIce = mysqli_real_escape_string($cnx,$_POST['brokerIce']);
    $query = "UPDATE `broker` SET `nom`='$brkNom',`prenom`='$brkPrenom',`phone`='$brkPhone',`address`='$brkAdr',`brokerIce`='$brokerIce' WHERE id=$id";
    $res = mysqli_query($cnx,$query);
    $fullBrokerName = $brkNom .' '. $brkPrenom;
    if($res){
        userBroker_history($user_id,$fullBrokerName,'Update');
        $data = array('status'=>'success');
        echo json_encode($data);
    }else{
        $data = array('status'=>'failed');
        echo json_encode($data);
    }
}

?>