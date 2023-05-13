<?php
    include 'includes/config.php';
    include 'functions.php';

  
    if($_POST){
        
        $invoice_number = $_POST["invoice_number"];
        $client_id = getClientId($_POST["client_id"],$_POST["client_type"]);
        $invoice_comment = $_POST['invoice_comment'];
        $label_subTotal = floatval(trim(str_replace('DH',"",$_POST['labelSubTotal'])));
        // $date = date_create($_POST["due_date"]);
        // $due_date = date_format($date,"Y/m/d");
        $label_discount = floatval(trim(str_replace('DH',"",$_POST['labelDiscount'])));
        $label_netTotal = floatval(trim(str_replace('DH',"",$_POST['labelInvoiceTotal'])));
        $tva_checked = $_POST['tva_checked']=='true'?1:0;
        $objet_name = $_POST['objet_name'];
        $located_txt = $_POST['located_txt'];
      
        // echo $label_netTotal;
        $query = "INSERT INTO `invoice`(`id`, `F_number`, `id_client`, `sub_total`, `discount`, `net_total`, `type`, `status`,`remove_tva`, `comment`,`objet`,`located`)
                             VALUES (null,'$invoice_number','$client_id','$label_subTotal','$label_discount','$label_netTotal','encours','encours','$tva_checked','$invoice_comment','$objet_name','$located_txt')";
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
            $query = "INSERT INTO `detail_invoice`(`id`, `id_invoice`, `service_name`, `prix`, `quantity`, `discount`,`unit`,`ref`) VALUES (null,'$last_id','".$val["serviceName"]."','".$val["price"]."','".$val["quantity"]."', '$discount','".$val["unit"]."','".$val["srvRef"]."')";
            $res = mysqli_query($cnx,$query);
            
        }
        
        // $user_id = $_SESSION['user_id'];

        // //adding to invoice_payment
        // if($_POST['invoice_payment']!=""){
        //     $invoice_payment = floatval($_POST['invoice_payment']) > $label_netTotal ? $label_netTotal : floatval($_POST['invoice_payment']);
        //     $payment_method = $_POST['payment_method'];
        //     $pay_giver = $_POST["invoice_pay_giver"];
        //     $user_id = $_SESSION['user_id'];
        //     $query = "INSERT INTO `invoice_payments`(`id`, `id_invoice`, `prix`, `pay_method`,`user_id`) VALUES (null,'$last_id','$invoice_payment','$payment_method',$user_id)";
        //     mysqli_query($cnx,$query);
        //     $pay_id = mysqli_insert_id($cnx);
        //     // acceptPayment($last_id);
            
        //     addReceipt($pay_id,$pay_giver);
        //     // if($invoice_payment==$label_netTotal){
        //     //     updateInvoicePaidStatus($last_id);
        //     // }

        //     //user history
        //     userInvoice_history($user_id,$last_id,"Paiement Effectué");
        // }

        //  adding to user_invoice for history...
        $user_id = $_SESSION['user_id'];
        userInvoice_history($user_id,$last_id,"Add");

        //adding to notifications
        $current_date = date('Y-m-d H:i:s');
        $query = "INSERT INTO `notifications`(`id_document`, `date`) VALUES ('$last_id','$current_date')";
        $res = mysqli_query($cnx,$query);

        if($res){
          
            $data = array('status'=>'success');
            echo json_encode($data);
            
        }
        else{
           

            $data = array('status'=>'failed');
            echo json_encode($data);
        }
    }
    else{
        header("location:invoice-list.php");
        exit();
    }
     
    
?>