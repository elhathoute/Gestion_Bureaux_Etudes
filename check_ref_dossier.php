<?php
include "includes/config.php";
include "./functions.php";

if($_POST){

    $ref_dossier = $_POST["ref_dossier"];
    $ds_ref=$_POST["ds_ref"];
    $res=CheckRefDossier($ref_dossier,$ds_ref);
    if($res>0){
        $data = array('status'=>'success');
        echo json_encode($data);
    }else{
        $data = array('status'=>'failed');
        echo json_encode($data);
    }
}