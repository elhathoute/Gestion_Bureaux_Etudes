<?php
    include 'includes/config.php';
    include 'functions.php';

        if($_POST){

           $devis_id=$_POST['devis_id'];
           $unique_service_id=$_POST['unique_service_id'];

        //    get count of dossier

        $count_dossier = getCountServiceDossier($devis_id,$unique_service_id);

          
        if($count_dossier>=0){
            $data = array('status'=>'success','count'=>$count_dossier);
            echo json_encode($data);
        }else{
            $data = array('status'=>'failed');
            echo json_encode($data);
        }


        }
    ?>