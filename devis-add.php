<?php
    include 'includes/config.php';
    include 'functions.php';

    
    if($_POST){
        
        //declaring Vars
        $devis_number = $_POST["devis_number"];
        $client_id = getClientId($_POST["client_id"],$_POST["client_type"]);
        $devis_comment = $_POST['devis_comment'];
        $label_subTotal = floatval(trim(str_replace('DH',"",$_POST['labelSubTotal'])));
        // $label_discount = $_POST['labelDiscount'];
        $label_discount = floatval(trim(str_replace('DH',"",$_POST['labelDiscount'])));
        $label_netTotal = floatval(trim(str_replace('DH',"",$_POST['labelDevisTotal'])));
        $tva_checked = $_POST['tva_checked']=='true'?1:0;
        
        $objet_name = $_POST['objet_name'];
        $located_txt = $_POST['located_txt'];
        if(isset($_POST['brkId'])){
            $brkId = $_POST['brkId'];
        }
        
        $user_role=getUserRole($_SESSION['user_id']);
        // ($user_role['role_name']=="assistant") ?  $type="encours" : $type="Approved";
        // ($user_role['role_name']=="assistant") ?  $status="encours" : $status="accepter";
        $query = "INSERT INTO `devis`(`id`, `number`, `id_client`, `sub_total`, `discount`, `net_total`, `type`, `status`,`remove_tva`, `comment`,`objet`,`located`) VALUES (null,'$devis_number','$client_id','$label_subTotal','$label_discount','$label_netTotal','Approved','accepter','$tva_checked','$devis_comment','$objet_name','$located_txt')";
        $res = mysqli_query($cnx,$query);
        
        $last_id;
        if($res){
            $last_id = mysqli_insert_id($cnx);
        }
        // echo $last_id;
        
        $tableData = $_POST['tableData'];
        $tableData = json_decode($tableData,TRUE);
        
        
        $user_id = $_SESSION['user_id'];

         //adding to user_devis for history...
        $user_id = $_SESSION['user_id'];
        userDevis_history($user_id,$last_id,'Add');

        //adding to broker_devis
            $dBrk_id = '';
        if(isset($brkId)){
            $dBrk_id = bindBrokerDevis($brkId,$last_id);
        }
        $res1;

        //adding to notifications
        $current_date = date('Y-m-d H:i:s');
        $query = "INSERT INTO `notifications`(`id_document`, `date`) VALUES ('$last_id','$current_date')";
        $res = mysqli_query($cnx,$query);

        $empl=1;
        foreach ($tableData as $key=>$val) {
            
        for($i=0;$i<intval($val['quantity']);$i++) {
            $discount = $val['discount']==""?0: floatval(trim(str_replace('DH',"",$val['discount'])));
            $query = "INSERT INTO `detail_devis`(`id`, `id_devis`, `service_name`, `prix`, `quantity`, `discount`,`unit`,`ref`,`srv_unique_id`,`empl`) VALUES (null,'$last_id','".mysqli_real_escape_string($cnx,$val["serviceName"])."','".floatval($val["price"])."','".$val["quantity"]."', '$discount','".$val["unit"]."','".$val["srvRef"]."',$last_id+$key+1,$empl)";
            $res1 = mysqli_query($cnx,$query);
        }
        $empl++;
    }

        if($res1){
            $data = array('status'=>'success',"dBrk_id"=>$dBrk_id,"devis_id"=>$last_id);
            echo json_encode($data);
        }
        else{
            $data = array('status'=>'failed');
            echo json_encode($data);
        }
    

    }else{
        header("location:devis-view.php");exit();
    }

?>