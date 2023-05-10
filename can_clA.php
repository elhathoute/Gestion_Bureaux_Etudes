<?php
    include 'includes/config.php';
    include 'functions.php';

    if($_POST){
        $res;
        $detail_devis_id = $_POST['doc_id'];

        // $clientApprove = getDevisById($devis_id)['client_approve'];
        $query = "SELECT * FROM `detail_devis` WHERE `id`='$id'";
        $res = mysqli_query($cnx,$query);
        $row = mysqli_fetch_assoc($res);
        $service_approve = $row['confirmed'];
        $devis_id = $row['id_devis'];

        
        // adding to user_devis for history...
        if($service_approve=="1"){
            $user_id = $_SESSION['user_id'];
            userDevis_history($user_id,$devis_id,'Devis canceled');
        }
    

        $query = "UPDATE `detail_devis` SET `confirmed`='0' WHERE `id`='$detail_devis_id' ";
        $res= mysqli_query($cnx,$query);
    
        if($res){
            $data = array('status'=>'success');
            echo json_encode($data);
        }else{
            $data = array('status'=>'failed');
            echo json_encode($data);
        }
    }




?>