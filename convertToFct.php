<?php
    include 'includes/config.php';
    include 'functions.php';

if($_POST){
    $devis_id = $_POST['devis_id'];
    $devis = getDevisById($devis_id);
    $details_devis = getApprovedDevisDetails($devis_id);
    if(count($details_devis)>0){
        $invoice_number = sprintf("%03d", getInvoiceNumber()) . '/' . date('Y');
        $invoice_comment =mysqli_real_escape_string($cnx, $devis['comment']);
        $sub_total = $devis['sub_total'];
        $due_date = date("Y-m-d");
        $devis_discount = $devis['discount'];
        $net_total = $devis['net_total'];
        $remove_tva = $devis['remove_tva'];
        $objet_name = mysqli_real_escape_string($cnx,$devis['objet']);
        $located = $devis['located'];
        $client_id =$devis['id_client'];
        $user_role=getUserRole($_SESSION['user_id']);
        //insert to invoice
        if(isset($_POST['broker_id'])){
                $devis = getbrkDevisById($devis_id);
                $devis_discount = $devis['brkdiscount'];
                $net_total = $devis['brknettotal'];
                $sub_total = $devis['brkksubtotal'];
                $broker_id=$_POST['broker_id'];
                $query = "INSERT INTO `invoice`(`id`, `F_number`, `id_client`, `sub_total`, `discount`, `net_total`, `type`, `status`,`remove_tva`, `comment`,`objet`,`located`,`pour`,`devis_id`,`broker_id`) VALUES (null,'$invoice_number','$client_id','$sub_total','$devis_discount','$net_total','Approved','accepter','1','$invoice_comment','$objet_name','$located','INT','$devis_id','$broker_id')";
            }else{
                $query = "INSERT INTO `invoice`(`id`, `F_number`, `id_client`, `sub_total`, `discount`, `net_total`, `type`, `status`,`remove_tva`, `comment`,`objet`,`located`,`pour`,`devis_id`) VALUES (null,'$invoice_number','$client_id','$sub_total','$devis_discount','$net_total','Approved','accepter','$remove_tva','$invoice_comment','$objet_name','$located','MO','$devis_id')";
            }
            $res = mysqli_query($cnx,$query);
            $last_id;
            if($res){
                $last_id = mysqli_insert_id($cnx);
            }
            //insert data to invoice detail
            if(isset($_POST['broker_id'])){
                $details_devis=getApprovedDevisDetailsBrk($devis_id);
                foreach ($details_devis as $val) {
                    $query = "INSERT INTO `detail_invoice`(`id`, `id_invoice`, `service_name`, `prix`, `quantity`, `discount`,`unit`,`ref`) VALUES (null,'$last_id','".$val[2]."','".$val[19]."','".$val[4]."','".$val[20]."','".$val[6]."','".$val[7]."')";
                    $res = mysqli_query($cnx,$query); 
                }
            }else{
                foreach ($details_devis as $val) {
                    $query = "INSERT INTO `detail_invoice`(`id`, `id_invoice`, `service_name`, `prix`, `quantity`, `discount`,`unit`,`ref`) VALUES (null,'$last_id','".$val[2]."','".$val[3]."','".$val[4]."','".$val[5]."','".$val[6]."','".$val[7]."')";
                    $res = mysqli_query($cnx,$query); 
                }
            }
            $user_id = $_SESSION['user_id'];
            userInvoice_history($user_id,$last_id,"Add");
            //insert to notification
            $current_date = date('Y-m-d H:i:s');
            $query = "INSERT INTO `notifications`(`id_document`, `date`) VALUES ('$last_id','$current_date')";
            $res = mysqli_query($cnx,$query);
        
             // update devis set devis is converted to facture
            if(isset($_POST['broker_id'])){
                $broker_id=$_POST['broker_id'];
                // $query2 = "UPDATE `broker_devis` SET `is_facture`='1' WHERE id_devis=$devis_id";
                $query2 = "UPDATE `devis` SET `is_facture`='1' WHERE id=$devis_id";
                $res2 = mysqli_query($cnx,$query2);
                if($res){
                    $data = array('status'=>'success','invoice_id'=>$last_id,'client_id'=>$client_id,'broker_id'=>$broker_id);
                    echo json_encode($data);
                }else{
                    $data = array('status'=>'failed');
                    echo json_encode($data);
                }
            }else{
                $query2 = "UPDATE `devis` SET `is_facture`='1' WHERE id=$devis_id";
                $res2 = mysqli_query($cnx,$query2);
                
                if($res){
                    $data = array('status'=>'success','invoice_id'=>$last_id,'client_id'=>$client_id);
                    echo json_encode($data);
                }else{
                    $data = array('status'=>'failed');
                    echo json_encode($data);
                }
            }
        // }
    }else{
        $data = array('status'=>'emptyDevis');
        echo json_encode($data);
    }
}
