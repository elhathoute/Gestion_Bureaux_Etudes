<?php
    include 'includes/config.php';
    include 'functions.php';

    $client_id = $_POST["clientId"];
    $services_total =$_POST["hiddenTotalValue"];
    $payment_giver =$_POST["payment_giver"];
    $filter_type =$_POST["filter_type"];
    $payment_method = $_POST["payment-method"];
    $allDisplayedServicesId = $_POST["invoiceId"];
    // $clientPaymentAmount=$_POST["paymentClientPrice"]; //paymentClientPrice → the price that the client pay
    $payment = floatval($_POST["paymentClientPrice"]); // floatval => Get float value of a variable
    
    // var_dump($_POST['dossiers']);
    // die();
    //if any of the services is checked
    if(isset($_POST['ids'])){
        $dossiers_id = $_POST["dossiers"];
        $allCheckedServicesId = $_POST["ids"];
        foreach($allCheckedServicesId as $index=>$dev_id){
            // $query = "SELECT * FROM `detail_devis` WHERE `id`='$dev_id'";
            $query = "SELECT detail_devis.*, dossier.N_dossier
            FROM detail_devis
            LEFT JOIN dossier ON detail_devis.id = dossier.id_service
            WHERE detail_devis.id = '$dev_id'";
            $res =mysqli_query($cnx,$query);
            $row = mysqli_fetch_assoc($res);
            $selected_detail = getDetailDevisById($dev_id); //=> "SELECT * FROM `detail_devis` WHERE `id`='$dev_id'
            $service_price = $selected_detail['prix']; // price of the service
            $devisRow = getDevisById($row['id_devis']); //=> SELECT * FROM `devis` WHERE `id`='$id';
            $detail_price = ($devisRow['remove_tva'] != 1) ? round(($service_price*0.2) + $service_price , 2) : round($service_price , 2);//price of the service depending of there is a tva or not
            $price = $payment>=$detail_price?$detail_price:$payment;
            $devis_id=$selected_detail['id_devis'];
            $dossier_id =$dossiers_id[$index];
            // var_dump($detail_price);
                
            if($payment >= $detail_price){
                $payment=$payment-$detail_price;
                $montant_paye=$detail_price;
                payDevis($dev_id,$payment_method,$devis_id,$payment_giver,$dossier_id,$detail_price,$montant_paye);
            }else if($payment < $detail_price){
                if($payment==0){
                    die();
                }
                $montant_paye = $payment;  
                payDevis($dev_id,$payment_method,$devis_id,$payment_giver,$dossier_id,$detail_price,$montant_paye);
                die();
            }
            
                // if($dRef['N_dossier'] != NULL){
                    //     var_dump($dRef['N_dossier']); 
                    // }else{
                        //     var_dump('-');
                        
        }
                    // die();


}elseif($_POST){
        //select the devis id of the services depending on detai_devis.id and pay them
        $dossier_id = 0;
        $allDisplayedServicesId = $_POST["invoiceId"];
        foreach($allDisplayedServicesId as $index =>$dev_id){
            $query = "SELECT detail_devis.*, dossier.N_dossier
            FROM detail_devis
            LEFT JOIN dossier ON detail_devis.id = dossier.id_service
            WHERE detail_devis.id = '$dev_id'";
            $res =mysqli_query($cnx,$query);
            $row = mysqli_fetch_assoc($res);
            $selected_detail = getDetailDevisById($dev_id); //=> "SELECT * FROM `detail_devis` WHERE `id`='$dev_id'
            $service_price = $selected_detail['prix']; // price of the service
            $devisRow = getDevisById($row['id_devis']); //=> SELECT * FROM `devis` WHERE `id`='$id';
            $detail_price = ($devisRow['remove_tva'] != 1) ? round(($service_price*0.2) + $service_price , 2) : round($service_price , 2);//price of the service depending of there is a tva or not
            $price = $payment>=$detail_price?$detail_price:$payment;
            $devis_id=$selected_detail['id_devis'];
            // $dossier_id =$dossiers_id[$index];
            $servicePaymentDetails=getPaymentDetails($dev_id);
            // var_dump($servicePaymentDetails);
            // die();

            if(!is_null($servicePaymentDetails )&& $servicePaymentDetails['avanceSum']>0){
                if($payment >= $detail_price){
                    $priceLeft =$detail_price - $servicePaymentDetails['avanceSum'];
                    $payment=$payment - $priceLeft;
                    $montant_paye =$priceLeft;
                    payDevis($dev_id,$payment_method,$devis_id,$payment_giver,$dossier_id,$detail_price,$montant_paye);
                }else if($payment < $detail_price){
                    if($payment==0){
                        die();
                    }
                    $avanceSum= $payment +$servicePaymentDetails['avanceSum'];
                    if($avanceSum<=$detail_price){
                        $montant_paye= $payment;
                        payDevis($dev_id,$payment_method,$devis_id,$payment_giver,$dossier_id,$detail_price,$montant_paye);
                        die();
                    }
                    $priceLeft =$detail_price - $servicePaymentDetails['avanceSum'];
                    $payment =$payment -$priceLeft;
                    $montant_paye = $priceLeft;  
                    payDevis($dev_id,$payment_method,$devis_id,$payment_giver,$dossier_id,$detail_price,$montant_paye);
                    // die();
                }
            }else{
                if($payment >= $detail_price){
                    $payment=$payment-$detail_price;
                    $montant_paye=$detail_price;
                    payDevis($dev_id,$payment_method,$devis_id,$payment_giver,$dossier_id,$detail_price,$montant_paye);
                }else if($payment < $detail_price){
                    if($payment==0){
                        die();
                    }
                    $montant_paye = $payment;  
                    payDevis($dev_id,$payment_method,$devis_id,$payment_giver,$dossier_id,$detail_price,$montant_paye);
                    die();
                }
            }

        }


    
    
        // $devis_res = getFirstPayDevis($client_id);
        // $devisRow = mysqli_fetch_assoc($devis_res);
        // $devis_detail_id_arr = getDevisDetails($devisRow); //it gives you the id of detail_devis
    
    
    
    
    
        

    }
?>