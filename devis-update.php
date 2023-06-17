<?php
    include 'includes/config.php';
    include 'functions.php';




    if($_POST){
      
        $devis_id= $_POST['devis_id'];
        
       
        // get all services of devis
        // $originalServices = getDevisAllDetails($devis_id);
       
        
        //service info 
        // $devisStatus = $_POST['devisStatus'];
        $devis_comment =mysqli_real_escape_string($cnx,$_POST['devis_comment']);
        $objet_name = mysqli_real_escape_string($cnx,$_POST['objet_name']);
        $located = mysqli_real_escape_string($cnx,$_POST['located_txt']);
        $label_subTotal = floatval(trim(str_replace('DH',"",$_POST['labelSubTotal'])));
        $label_discount = floatval(trim(str_replace('DH',"",$_POST['labelDiscount'])));
        $label_netTotal = floatval(trim(str_replace('DH',"",$_POST['labelDevisTotal'])));
        $espace = mysqli_real_escape_string($cnx,$_POST['espace']);
        $hauteur = mysqli_real_escape_string($cnx,$_POST['hauteur']);
        // update devis
        $query = "UPDATE `devis` SET  `sub_total`='$label_subTotal', `discount`='$label_discount', `net_total`='$label_netTotal', `type`='Approved', `status`='accepter', `comment`='$devis_comment', `objet`='$objet_name',`located`='$located',`hauteur`='$hauteur',`espace`='$espace' WHERE id='$devis_id'";
        $res = mysqli_query($cnx,$query);
     
        //adding to user_devis for history...
        $user_id = $_SESSION['user_id'];
        userDevis_history($user_id,$devis_id,'Update');
        // get all services from js
        $tableData = $_POST['tableData'];
        $updatedServices = json_decode($tableData,TRUE);
        // original services
        $res;
       for($j=0;$j<count( $updatedServices );$j++){ 
        // update values

        $qte_update  = intval($updatedServices[$j]['quantity']);
        $service_unique_id_update = $updatedServices[$j]['serviceUniqueId'];
        $service_name =  mysqli_real_escape_string($cnx,$updatedServices[$j]['serviceName']);
        $price = $updatedServices[$j]['price'];
        $qte = $updatedServices[$j]['quantity'];
        $unit = mysqli_real_escape_string($cnx,$updatedServices[$j]['unit']);
        $ref = $updatedServices[$j]['srvRef'];
       $discount = $updatedServices[$j]['discount']==""?0:$updatedServices[$j]['discount'];
        $originalServices = getDevisAllDetailsDistinct($devis_id,$updatedServices[$j]['serviceUniqueId']);

        $all_srv_unique_id_devis =getAllServiceUniqueIdDevis($devis_id);
        // die(var_dump($originalServices[0]['srv_unique_id']));
        // die(var_dump(array_column($updatedServices,'serviceUniqueId')));
        // die(var_dump($originalServices[0]));
         if(!empty($originalServices) && in_array($originalServices[0]['srv_unique_id'],array_column($updatedServices,'serviceUniqueId')))
         {
        
        foreach($originalServices  as $index => $originalService ){
            $qte_origine = intval($originalService['quantity']);
 
            // if qte_origine=qte_update (UPDATE)
            if($qte_origine==$qte_update){
         
                $query = "UPDATE `detail_devis` SET `service_name`='$service_name',`prix`='$price',`quantity`='$qte',`discount`='$discount',`unit`='$unit',`ref`='$ref' WHERE `id_devis`=$devis_id AND `srv_unique_id` = '$service_unique_id_update'";
                $res = mysqli_query($cnx, $query);

            }

                // (Insert+update)
                else if($qte_origine  < $qte_update){
                    $new_qte = $qte_update - $qte_origine;
                    
                    $confirmed = getConfirmedApprovedService($devis_id,$service_unique_id_update)['confirmed'];
                    $empl = getConfirmedApprovedService($devis_id,$service_unique_id_update)['empl'];
                    // $empl = 1;
                    
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
            

                    // for ($i = 0; $i < $new_qte; $i++) {

                        $query = "DELETE FROM `detail_devis` WHERE `id_devis`=$devis_id AND `srv_unique_id` = '$service_unique_id_update' AND `approved`='0' LIMIT $new_qte";
                        $res=mysqli_query($cnx, $query);
       
                    // }
                 
               
                }

        }
    
    }
    // else(!in_array($originalServices[0]['srv_unique_id'],array_column($updatedServices,'serviceUniqueId'))){
        else{

                 $empl=$j+1;

                    for ($i = 0; $i < $qte_update; $i++) {
                        // $approved = getConfirmedApprovedService($devis_id,$service_unique_id_update)['approved'];
                        // die($approved);
                        $query = "INSERT INTO `detail_devis`(`id_devis`, `service_name`, `prix`, `quantity`, `discount`, `unit`, `ref`,`confirmed`,`approved`, `srv_unique_id`, `empl`) VALUES ('$devis_id','$service_name','$price','$qte_update','$discount','$unit','$ref','0','0','$service_unique_id_update','$empl')";
                        $res=mysqli_query($cnx, $query);

                    }
                    //  $empl++;
        }
 
    }
    // 
        //    die( var_dump($all_srv_unique_id_devis));
        //    die( var_dump(array_column($updatedServices,'serviceUniqueId')));
        $droped_array_services=array_diff($all_srv_unique_id_devis,array_column($updatedServices,'serviceUniqueId'));
        foreach($droped_array_services as $key=>$droped_array_service){
            // die(var_dump($key.'vl'.$droped_array_service));
            
            $query = "DELETE FROM `detail_devis` WHERE  `id_devis`=$devis_id  AND  `srv_unique_id`=$droped_array_service";
            $res=mysqli_query($cnx, $query);

        
            // die(var_dump($key.'='.$droped_array_service));
            // die()
            for($m=0;$m<count(getAllServiceAfterDeletedService($devis_id,$key));$m++){
                
                $empl=$key+$m+1;
                $unique_serv_id=$droped_array_service+$m;

                $srv_unique_id_up=getAllServiceAfterDeletedService($devis_id,$key)[$m];
                $query = "UPDATE  `detail_devis` set `empl`=$empl ,`srv_unique_id`= $unique_serv_id WHERE `id_devis`='$devis_id' AND  `srv_unique_id`=$srv_unique_id_up";
                $res = mysqli_query($cnx,$query);

            }
            // $empl++;

            // die(var_dump(getAllServiceAfterDeletedService($devis_id,$key)));
            // die(var_dump(getAllServiceUniqueIdDevis($devis_id)));
            
        }
        // for($k=0;$k<count($droped_array_services);$k++){
        //     die(var_dump($droped_array_services[$k]));
        //     //  $query = "DELETE FROM `detail_devis` `id_devis`=$devis_id where `srv_unique_id`=$drop_srv_id";
        //     // $res=mysqli_query($cnx, $query);
        // }
        //    die(var_dump(array_diff($all_srv_unique_id_devis,array_column($updatedServices,'serviceUniqueId'))));
        //    for($k=0;$k<count($all_srv_unique_id_devis);$k++){

        //    }
        //    else if(!in_array($updatedServices,$all_srv_unique_id_devis)){
        //     $drop_srv_id=$updatedServices[$j]['serviceUniqueId'];
        //     $query = "DELETE FROM `detail_devis` `id_devis`=$devis_id where `srv_unique_id`=$drop_srv_id";
        //     $res=mysqli_query($cnx, $query);
            
        // }
        $dBrk_id = -(isset(getBroker_devisData($devis_id)['id'])) ? getBroker_devisData($devis_id)['id'] : '';

        if($res){
            $data = array('status'=>'success','dBrk_id'=>$dBrk_id);
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