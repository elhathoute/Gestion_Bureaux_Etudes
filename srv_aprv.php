<?php
    include 'includes/config.php';
    include 'functions.php';
    

    if($_POST){
        $srv_id = $_POST['srv_id'];
        $n_dossier = $_POST['n_dossier'];
        $ds_ref=$_POST['ds_ref'];

        $query = "UPDATE `detail_devis` SET `approved`='1' WHERE `id`='$srv_id' ";
        $res= mysqli_query($cnx,$query);
        
        //save dossier to db
        saveDossier($srv_id,$n_dossier,$ds_ref);

        if($res){
            $data = array('status'=>'success');
            echo json_encode($data);
        }else{
            $data = array('status'=>'failed');
            echo json_encode($data);
        }
    }




?>