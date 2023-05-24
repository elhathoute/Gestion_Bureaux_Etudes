<?php
    include 'includes/config.php';
    include 'functions.php';




    if($_POST){
      
        $devis_id= $_POST['devis_id'];
        
       
        // get all services of devis
        // $originalServices = getDevisAllDetails($devis_id);
        $originalServices = getDevisAllDetailsDistinct($devis_id);
       
        
        //service info 
        // $devisStatus = $_POST['devisStatus'];
        $devis_comment = $_POST['devis_comment'];
        $objet_name = $_POST['objet_name'];
        $located = $_POST['located_txt'];
        $label_subTotal = floatval(trim(str_replace('DH',"",$_POST['labelSubTotal'])));
        $label_discount = floatval(trim(str_replace('DH',"",$_POST['labelDiscount'])));
        $label_netTotal = floatval(trim(str_replace('DH',"",$_POST['labelDevisTotal'])));
        // update devis
        $query = "UPDATE `devis` SET  `sub_total`='$label_subTotal', `discount`='$label_discount', `net_total`='$label_netTotal', `type`='Approved', `status`='accepter', `comment`='$devis_comment', `objet`='$objet_name',`located`='$located' WHERE id='$devis_id'";
        $res = mysqli_query($cnx,$query);
     
        //adding to user_devis for history...
        $user_id = $_SESSION['user_id'];
        userDevis_history($user_id,$devis_id,'Update');
        // get all services from js
        $tableData = $_POST['tableData'];
        $updatedServices = json_decode($tableData,TRUE);
        // original services
        $res;
        foreach($originalServices  as $index => $originalService ){
            $qte_update  = intval($updatedServices[$index]['quantity']);
            $qte_origine = intval($originalService['quantity']);
            // die(var_dump($qte_update));
           
            $service_unique_id_update = $updatedServices[$index]['serviceUniqueId'];
            $service_name = $updatedServices[$index]['serviceName'];
            $price = $updatedServices[$index]['price'];
            $qte = $updatedServices[$index]['quantity'];
            $unit = $updatedServices[$index]['unit'];
            $ref = $updatedServices[$index]['srvRef'];
           $discount = $updatedServices[$index]['discount']==""?0:$updatedServices[$index]['discount'];
          
           
            // if qte_origine=qte_update (UPDATE)
            if($qte_origine==$qte_update){
         
                $query = "UPDATE `detail_devis` SET `service_name`='$service_name',`prix`='$price',`quantity`='$qte',`discount`='$discount',`unit`='$unit',`ref`='$ref' WHERE `id_devis`=$devis_id AND `srv_unique_id` = '$service_unique_id_update'";
                $res = mysqli_query($cnx, $query);

            }

                // (Insert+update)
                else if($qte_origine  < $qte_update){
                   
                    $new_qte = $qte_update - $qte_origine;
              
                    $empl = 1;
                $confirmed = getConfirmedApprovedService($devis_id,$service_unique_id_update)['confirmed'];
    
                    $query = "UPDATE `detail_devis` SET `service_name`='$service_name',`prix`='$price',`quantity`='$qte_update',`discount`='$discount',`unit`='$unit',`ref`='$ref' WHERE `id_devis`=$devis_id AND `srv_unique_id` = '$service_unique_id_update'";
                    $res=mysqli_query($cnx, $query);
            

                    for ($i = 0; $i < $new_qte; $i++) {
                        // $approved = getConfirmedApprovedService($devis_id,$service_unique_id_update)['approved'];
                        // die($approved);
                        $query = "INSERT INTO `detail_devis`(`id_devis`, `service_name`, `prix`, `quantity`, `discount`, `unit`, `ref`,`confirmed`,`approved`, `srv_unique_id`, `empl`) VALUES ('$devis_id','$service_name','$price','$qte_update','$discount','$unit','$ref','$confirmed','0','$service_unique_id_update','$empl')";
                        $res=mysqli_query($cnx, $query);

                    }
                     $empl++;
               
                    // for

                    // die(var_dump('done'));
                }
                else if($qte_origine  > $qte_update){

                    $new_qte = $qte_origine-$qte_update;
                    $query = "UPDATE `detail_devis` SET `service_name`='$service_name',`prix`='$price',`quantity`='$qte_update',`discount`='$discount',`unit`='$unit',`ref`='$ref' WHERE `id_devis`=$devis_id AND `srv_unique_id` = '$service_unique_id_update'";
                    $res= mysqli_query($cnx, $query);
            

                    for ($i = 0; $i < $new_qte; $i++) {
                        $query = "DELETE FROM `detail_devis` WHERE `id_devis`=$devis_id AND `srv_unique_id` = '$service_unique_id_update' AND `approved`='0' LIMIT $new_qte";
                        $res=mysqli_query($cnx, $query);
       

                    }
                 
               
                }

        }

        if($res){
            $data = array('status'=>'success');
            echo json_encode($data);
        }else{
            $data = array('status'=>'failed');
            echo json_encode($data);
        }

        // foreach($originalServices as $index => $originalService){
        //     $updated_service = $updatedServices[$index];
        //     // die(var_dump($updated_service));
        //     if($updated_service === null){
        //          // selete service from db
        //         $service_id = $originalService['id'];
        //         $query = "DELETE FROM `detail_devis` WHERE `id` = $service_id";
        //         mysqli_query($cnx,$query);

        //     }
        //     else{
              
                
        //         //check for updates to the service
        //         $discount = $updated_service['discount']=="" ? 0: $updated_service['discount'];
        //         // die(var_dump($originalService['service_name'].'='.$updated_service['serviceName'] ));
        //         if($originalService['service_name'] !== $updated_service['serviceName'] ||
        //             $originalService['prix'] !== $updated_service['price'] ||
        //             $originalService['quantity'] !== $updated_service['quantity'] ||
        //             $originalService['discount'] !== $discount || 
        //             $originalService['unit'] !== $updated_service['unit'] || 
        //             $originalService['ref'] !== $updated_service['srvRef']
        //         )
        //         {
        //             $service_id = $originalService['id'];
        //             $service_name = $updated_service['serviceName'];
        //             $price = $updated_service['price'];
        //             $qte = $updated_service['quantity'];
        //             $unit = $updated_service['unit'];
        //             $ref = $updated_service['srvRef'];
        //             // $empl = 1;
        //             // for ($i = 0; $i < $updated_service['quantity']; $i++) {
        //             $query = "UPDATE `detail_devis` SET `service_name`='$service_name',`prix`='$price',`quantity`='$qte',`discount`='$discount',`unit`='$unit',`ref`='$ref' WHERE `id` = '$service_id'";
        //             mysqli_query($cnx, $query);
        //             // }
        //             // $empl++;
        //         }
        //     }
        // }
        
        // foreach($updatedServices as $index => $updated_service){
        //     if($updated_service !== null && !isset($originalServices[$index])){
           
        //         $service_id = $originalService['id'];
        //         $service_name = $updated_service['serviceName'];
        //         $price = $updated_service['price'];
        //         $qte = $updated_service['quantity'];
        //         $unit = $updated_service['unit'];
        //         $ref = $updated_service['srvRef'];
        //         $discount = $updated_service['discount']==""?0:$updated_service['discount'];

        //         $query = "INSERT INTO `detail_devis`(`id_devis`, `service_name`, `prix`, `quantity`, `discount`, `unit`, `ref`) VALUES ('$devis_id','$service_name','$price','$qte','$discount','$unit','$ref')";
        //         mysqli_query($cnx, $query);
        //     }
        // }


        
        
        // if($res){
        //     $data = array('status'=>'success');
        //     echo json_encode($data);
        // }else{
        //     $data = array('status'=>'failed');
        //     echo json_encode($data);
        // }
        


    }else{
        header("location:devis-view.php");exit();
    }


?>