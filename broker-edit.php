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
    $brkNom = $_POST["brkNom"];
    $brkPrenom = $_POST["brkPrenom"];
    $brkPhone = $_POST["brkPhone"];
    $brkAdr = $_POST["brkAdr"];
    $query = "UPDATE `broker` SET `nom`='$brkNom',`prenom`='$brkPrenom',`phone`='$brkPhone',`address`='$brkAdr' WHERE id=$id";
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