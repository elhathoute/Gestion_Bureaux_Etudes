<?php
    include 'includes/config.php';
    include 'functions.php';

    
    if($_POST){
    //    die(var_dump($_SESSION['user_id']));
        
        $invoice_id= $_POST['invoice_id'];
        
        $query = 'DELETE FROM `detail_invoice` WHERE id_invoice='.$invoice_id.'';
        $res = mysqli_query($cnx,$query);
    
        $invoiceStatus = $_POST['invoiceStatus'];
        $invoice_comment = $_POST['invoice_comment'];
        $objet_name = $_POST["objet_name"];
        // $date = date_create($_POST["due_date"]);
        // $due_date = date_format($date,"Y/m/d");
        $label_subTotal = floatval(trim(str_replace('DH',"",$_POST['labelSubTotal'])));
        // $label_discount = $_POST['labelDiscount'];
        $label_discount = floatval(trim(str_replace('DH',"",$_POST['labelDiscount'])));
        $label_netTotal = floatval(trim(str_replace('DH',"",$_POST['labelDevisTotal'])));
        $located_txt = $_POST['located_txt'];
        
        $query = "UPDATE `invoice` SET  `sub_total`='$label_subTotal', `discount`='$label_discount', `net_total`='$label_netTotal', `type`='encours', `status`='$invoiceStatus', `comment`='$invoice_comment',`objet`='$objet_name',`located`='$located_txt' WHERE id='$invoice_id'";
        $res = mysqli_query($cnx,$query);
        
        //adding to user_invoice for history...
        $user_id = $_SESSION['user_id'];
        userInvoice_history($user_id,$invoice_id,'Update');

        // echo $last_id;
        // echo $res;
        $tableData = $_POST['tableData'];
        $tableData = json_decode($tableData,TRUE);
        // $res='';
        foreach ($tableData as $val) {
        //  die(var_dump($val));
            
            $discount = $val['discount']=="" ? 0 : $val['discount'];
            $query = "INSERT INTO `detail_invoice`(`id`, `id_invoice`, `service_name`, `prix`, `quantity`, `discount`,`unit`,`ref`) 
            VALUES (null, '$invoice_id', '{$val["serviceName"]}', '{$val["price"]}', '{$val["quantity"]}', '$discount', '{$val["unit"]}', '{$val["srvRef"]}')";   
            $res = mysqli_query($cnx,$query);
            
        }
        
        
        if($res){
            $data = array('status'=>'success');
            echo json_encode($data);
        }else{
            $data = array('status'=>'failed');
            echo json_encode($data);
        }
        


    }else{
        header("location:invoice-list.php");
        exit();
    }
    

?>