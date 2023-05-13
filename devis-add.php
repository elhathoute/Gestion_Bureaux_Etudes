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


        $query = "INSERT INTO `devis`(`id`, `number`, `id_client`, `sub_total`, `discount`, `net_total`, `type`, `status`,`remove_tva`, `comment`,`objet`,`located`) VALUES (null,'$devis_number','$client_id','$label_subTotal','$label_discount','$label_netTotal','encours','encours','$tva_checked','$devis_comment','$objet_name','$located_txt')";
        $res = mysqli_query($cnx,$query);
        $last_id;
        if($res){
            $last_id = mysqli_insert_id($cnx);
        }
        // echo $last_id;
        
        $tableData = $_POST['tableData'];
        $tableData = json_decode($tableData,TRUE);
        $res;
        foreach ($tableData as $val) {
            $discount = $val['discount']==""?0:$val['discount'];
            $query = "INSERT INTO `detail_devis`(`id`, `id_devis`, `service_name`, `prix`, `quantity`, `discount`,`unit`,`ref`) VALUES (null,'$last_id','".$val["serviceName"]."','".floatval($val["price"])."','".$val["quantity"]."', '$discount','".$val["unit"]."','".$val["srvRef"]."')";
            $res = mysqli_query($cnx,$query);
            
        }

        /**
         * add payment if any 
        */

        $user_id = $_SESSION['user_id'];

        //adding to devis_payment
        // if($_POST['invoice_payment']!=""){
        //     $invoice_payment = floatval($_POST['invoice_payment']) > $label_netTotal ? $label_netTotal : floatval($_POST['invoice_payment']);
        //     $payment_method = $_POST['payment_method'];
        //     $pay_giver = $_POST["invoice_pay_giver"];
        //     $user_id = $_SESSION['user_id'];
        //     $query = "INSERT INTO `devis_payments`(`id`, `id_devis`, `prix`, `pay_method`,`user_id`) VALUES (null,'$last_id','$invoice_payment','$payment_method',$user_id)";
        //     mysqli_query($cnx,$query);
        //     $pay_id = mysqli_insert_id($cnx);
        //     // acceptPayment($last_id);
            
        //     addReceipt($pay_id,$pay_giver);
        //     // if($invoice_payment==$label_netTotal){
        //     //     updateInvoicePaidStatus($last_id);
        //     // }

        //     //user history
        //     userDevis_history($user_id,$last_id,"Paiement Effectué");
        // }






         //adding to user_devis for history...
        $user_id = $_SESSION['user_id'];
        userDevis_history($user_id,$last_id,'Add');

        //adding to broker_devis
            $dBrk_id = '';
        if(isset($brkId)){
            $dBrk_id = bindBrokerDevis($brkId,$last_id);
        }

        //adding to notifications
        $current_date = date('Y-m-d H:i:s');
        $query = "INSERT INTO `notifications`(`id_document`, `date`) VALUES ('$last_id','$current_date')";
        $res = mysqli_query($cnx,$query);

        if($res){
            $data = array('status'=>'success',"dBrk_id"=>$dBrk_id);
            echo json_encode($data);
        }else{
            $data = array('status'=>'failed');
            echo json_encode($data);
        }
    

    }else{
        header("location:devis-view.php");exit();
    }

?>