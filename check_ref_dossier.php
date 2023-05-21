<?php
include "includes/config.php";
include "./functions.php";

if($_POST){

    $ref_dossier = $_POST["ref_dossier"];
    $res=CheckRefDossier($ref_dossier);
    if($res>0){
        $data = array('status'=>'success');
        echo json_encode($data);
    }else{
        $data = array('status'=>'failed');
        echo json_encode($data);
    }
}