<?php
    include 'includes/config.php';
    include 'functions.php';




    if($_POST){
        // die($_POST['devisStatus']);
        // $devis_number = $_POST["devis_number"];
        // $client_id = $_POST["client_id"];
        $devis_id= $_POST['devis_id'];
        
        // $query = 'DELETE FROM `detail_devis` WHERE id_devis='.$devis_id.'';
        // $res = mysqli_query($cnx,$query);

        $originalServices = getDevisAllDetails($devis_id);

    
        $devisStatus = $_POST['devisStatus'];
        $devis_comment = $_POST['devis_comment'];
        $objet_name = $_POST['objet_name'];
        $located = $_POST['located_txt'];
        $label_subTotal = floatval(trim(str_replace('DH',"",$_POST['labelSubTotal'])));
        // $label_discount = $_POST['labelDiscount'];
        $label_discount = floatval(trim(str_replace('DH',"",$_POST['labelDiscount'])));
        $label_netTotal = floatval(trim(str_replace('DH',"",$_POST['labelDevisTotal'])));
        // echo "azeddine";
        // echo $label_netTotal;
        $user_role=getUserRole($_SESSION['user_id']);
        ($user_role['role_name']=="assistant") ?  $type="encours" : $type="Approved";

        $query = "UPDATE `devis` SET  `sub_total`='$label_subTotal', `discount`='$label_discount', `net_total`='$label_netTotal', `type`='$type', `status`='$devisStatus', `comment`='$devis_comment', `objet`='$objet_name',`located`='$located' WHERE id='$devis_id'";
        $res = mysqli_query($cnx,$query);
        
        //adding to user_devis for history...
        $user_id = $_SESSION['user_id'];
        userDevis_history($user_id,$devis_id,'Update');

        // echo $last_id;
        // echo $res;
        $tableData = $_POST['tableData'];
        $updatedServices = json_decode($tableData,TRUE);
        // $res='';

        foreach($originalServices as $index => $originalService){
            $updated_service = $updatedServices[$index];
            if($updated_service === null){
                //service has been deleted
                //delete service from Database
                $service_id = $originalService['id'];
                $query = "DELETE FROM `detail_devis` WHERE `id` = $service_id";
                mysqli_query($cnx,$query);

            }else{
                //check for updates to the service
                $discount = $updated_service['discount']==""?0:$updated_service['discount'];

                if($originalService['service_name'] !== $updated_service['serviceName'] ||
                    $originalService['prix'] !== $updated_service['price'] ||
                    $originalService['quantity'] !== $updated_service['quantity'] ||
                    $originalService['discount'] !== $discount || 
                    $originalService['unit'] !== $updated_service['unit'] || 
                    $originalService['ref'] !== $updated_service['srvRef']
                )
                {
                    $service_id = $originalService['id'];
                    $service_name = $updated_service['serviceName'];
                    $price = $updated_service['price'];
                    $qte = $updated_service['quantity'];
                    $unit = $updated_service['unit'];
                    $ref = $updated_service['srvRef'];

                    $query = "UPDATE `detail_devis` SET `service_name`='$service_name',`prix`='$price',`quantity`='$qte',`discount`='$discount',`unit`='$unit',`ref`='$ref' WHERE `id` = '$service_id'";
                    mysqli_query($cnx, $query);
                }
            }
        }

        foreach($updatedServices as $index => $updated_service){
            if($updated_service !== null && !isset($originalServices[$index])){
                $service_id = $originalService['id'];
                $service_name = $updated_service['serviceName'];
                $price = $updated_service['price'];
                $qte = $updated_service['quantity'];
                $unit = $updated_service['unit'];
                $ref = $updated_service['srvRef'];
                $discount = $updated_service['discount']==""?0:$updated_service['discount'];

                $query = "INSERT INTO `detail_devis`(`id_devis`, `service_name`, `prix`, `quantity`, `discount`, `unit`, `ref`) VALUES ('$devis_id','$service_name','$price','$qte','$discount','$unit','$ref')";
                mysqli_query($cnx, $query);
            }
        }




        // foreach ($updatedServices as $val) {
        //     $discount = $val['discount']==""?0:$val['discount'];
        //     $query = "INSERT INTO `detail_devis`(`id`, `id_devis`, `service_name`, `prix`, `quantity`, `discount`,`unit`,`ref`)
        //      VALUES (null,'$devis_id','".$val["serviceName"]."','".$val["price"]."','".$val["quantity"]."', '$discount','".$val["unit"]."','".$val["srvRef"]."')";
        //     $res = mysqli_query($cnx,$query);
            
        // }
        
        
        if($res){
            $data = array('status'=>'success');
            echo json_encode($data);
        }else{
            $data = array('status'=>'failed');
            echo json_encode($data);
        }
        


    }else{
        header("location:devis-view.php");exit();
    }












    
    // if($_POST){
    //     // $devis_number = $_POST["devis_number"];
    //     // $client_id = $_POST["client_id"];
    //     $devis_id= $_POST['devis_id'];
        
    //     $query = 'DELETE FROM `detail_devis` WHERE id_devis='.$devis_id.'';
    //     $res = mysqli_query($cnx,$query);
    
    //     $devisStatus = $_POST['devisStatus'];
    //     $devis_comment = $_POST['devis_comment'];
    //     $objet_name = $_POST['objet_name'];
    //     $label_subTotal = floatval(trim(str_replace('DH',"",$_POST['labelSubTotal'])));
    //     // $label_discount = $_POST['labelDiscount'];
    //     $label_discount = floatval(trim(str_replace('DH',"",$_POST['labelDiscount'])));
    //     $label_netTotal = floatval(trim(str_replace('DH',"",$_POST['labelDevisTotal'])));
    //     // echo "azeddine";
    //     // echo $label_netTotal;
    //     $query = "UPDATE `devis` SET  `sub_total`='$label_subTotal', `discount`='$label_discount', `net_total`='$label_netTotal', `type`='encours', `status`='$devisStatus', `comment`='$devis_comment', `objet`='$objet_name' WHERE id='$devis_id'";
    //     $res = mysqli_query($cnx,$query);
        
    //     //adding to user_devis for history...
    //     $user_id = $_SESSION['user_id'];
    //     userDevis_history($user_id,$devis_id,'Update');

    //     // echo $last_id;
    //     // echo $res;
    //     $tableData = $_POST['tableData'];
    //     $tableData = json_decode($tableData,TRUE);
    //     // $res='';
    //     foreach ($tableData as $val) {
    //         $discount = $val['discount']==""?0:$val['discount'];
    //         $query = "INSERT INTO `detail_devis`(`id`, `id_devis`, `service_name`, `prix`, `quantity`, `discount`,`unit`,`ref`) VALUES (null,'$devis_id','".$val["serviceName"]."','".$val["price"]."','".$val["quantity"]."', '$discount','".$val["unit"]."','".$val["srvRef"]."')";
    //         $res = mysqli_query($cnx,$query);
            
    //     }
        
        
    //     if($res){
    //         $data = array('status'=>'success');
    //         echo json_encode($data);
    //     }else{
    //         $data = array('status'=>'failed');
    //         echo json_encode($data);
    //     }
        


    // }else{
    //     header("location:devis-view.php");exit();
    // }

?>