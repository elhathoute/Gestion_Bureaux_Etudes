<?php
    include 'includes/config.php';
    include 'functions.php';

    if(isset($_POST["btn-approve-notif"])){
        if($_POST['doc_type']=="devis"){

            $current_date = date('Y-m-d H:i:s');
            $devis_id = $_POST['devis_id'];
            $query = "UPDATE `devis` SET `type`='Approved',`date_validation`='$current_date',`status`='accepter' WHERE `id`='$devis_id'";
            $res = mysqli_query($cnx,$query);
            if($res){
                $query = "UPDATE `notifications` SET `active`='0' WHERE `id_document` = '$devis_id' AND `active`='1'";
                mysqli_query($cnx,$query);
                
            }
        }elseif ($_POST['doc_type']=="invoice") {
            $current_date = date('Y-m-d H:i:s');
            $invoice_id = $_POST['invoice_id'];
            $query = "UPDATE `invoice` SET `type`='Approved',`date_validation`='$current_date',`status`='accepter' WHERE `id`='$invoice_id'";
            $res = mysqli_query($cnx,$query);
            if($res){
                $query = "UPDATE `notifications` SET `active`='0' WHERE `id_document` = '$invoice_id' AND `active`='1'";
                mysqli_query($cnx,$query);    
            }
        }elseif($_POST['doc_type']=="payment"){
            $devis_id = $_POST['id_devis'];
            $detail_id = $_POST['id_detail'];

            $query = "UPDATE `notifications` SET `active`='0' WHERE `id_document` = '$detail_id' AND `active`='1'";
            mysqli_query($cnx,$query);
            
            acceptPayment($detail_id);
            // setServiceNotif($detail_id);
        }
    }
    if(isset($_POST["btn-decline-notif"])){
        if($_POST['doc_type']=="devis"){

            $current_date = date('Y-m-d H:i:s');
            $devis_id = $_POST['devis_id'];
            $query = "UPDATE `devis` SET `type`='Declined',`date_validation`='$current_date',`status`='rejeter' WHERE `id`='$devis_id'";
            $res = mysqli_query($cnx,$query);
            if($res){
                $query = "UPDATE `notifications` SET `active`='0' WHERE `id_document` = '$devis_id' AND `active`='1'";
                mysqli_query($cnx,$query);
            }
        }elseif($_POST['doc_type']=="invoice"){
            $current_date = date('Y-m-d H:i:s');
            $invoice_id = $_POST['invoice_id'];
            $query = "UPDATE `invoice` SET `type`='Declined',`date_validation`='$current_date',`status`='rejeter' WHERE `id`='$invoice_id'";
            $res = mysqli_query($cnx,$query);
            if($res){
                $query = "UPDATE `notifications` SET `active`='0' WHERE `id_document` = '$invoice_id' AND `active`='1'";
                mysqli_query($cnx,$query);    
            }
        }elseif($_POST['doc_type']=="payment"){
            $devis_id = $_POST['id_devis'];
            $detail_id = $_POST['id_detail'];

            $query = "UPDATE `notifications` SET `active`='0' WHERE `id_document` = '$detail_id' AND `active`='1'";
            mysqli_query($cnx,$query);

            // $query = "UPDATE `invoice` SET `paid_inv`='0' WHERE `id`='$invoice_id'";
            // $res = mysqli_query($cnx,$query);
            
            declinePayment($detail_id);
            // setServiceNotif($detail_id);
        }
    }

    header('location:notifications.php');
?>