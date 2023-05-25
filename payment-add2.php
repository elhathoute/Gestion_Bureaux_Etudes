<?php
    include 'includes/config.php';
    include 'functions.php';
    if(!$_POST){
        header("Location: dashboard.php");
        die();
    }
    // var_dump($_POST);
    // die();
    $client_id = $_POST["clientId"];
    $services_total =$_POST["hiddenTotalValue"];
    $payment_giver =$_POST["payment_giver"];
    $filter_type =$_POST["filter_type"];
    $payment_method = $_POST["payment-method"];
    $allDisplayedServicesId = $_POST["servicesId"];
    $payment = floatval($_POST["paymentClientPrice"]); // floatval => Get float value of a variable → the price that the client pay
    
if(isset($_POST['supplierCheckbox']) && $_POST['supplier'] != '' && $_POST['paymentSupplier'] != '' && is_numeric($_POST['paymentSupplier']))
                    {
                        $supplier_id = $_POST['supplier'];
                        $supplier = getSupplierById($supplier_id);
                        $category = getSuppCatById($supplier['cat_id']);
                        if($category['type'] == 'Bureau de contrôle'){
                            $amount = $_POST['paymentSupplier'] + $supplier['sold'];
                            updateSupplierSold($supplier_id,$amount);
                            unset($_POST['supplierCheckbox']);
                        }
                    }
if(isset($_POST['ids'])){       //if any of the services is checked
    $dossiers_id = $_POST["dossiers"];
    $allCheckedServicesId = $_POST["ids"];
    $services_UId =$_POST["checkeduniqueIds"];
    foreach($allCheckedServicesId as $index=>$dev_id){
        $query = "SELECT detail_devis.*, dossier.N_dossier
        FROM detail_devis
        LEFT JOIN dossier ON detail_devis.id = dossier.id_service
        WHERE detail_devis.id = '$dev_id'";
        $res =mysqli_query($cnx,$query);
        $row = mysqli_fetch_assoc($res);
        $selected_detail = getDetailDevisById($dev_id); //=> "SELECT * FROM `detail_devis` WHERE `id`='$dev_id'
        $service_price =($selected_detail['discount']==0)? $selected_detail['prix']:$selected_detail['prix']-(($selected_detail['prix']*$selected_detail['discount'])/100);
        $devisRow = getDevisById($row['id_devis']); //=> SELECT * FROM `devis` WHERE `id`='$id';
        $detail_price = ($devisRow['remove_tva'] != 1) ? round(($service_price*0.2) + $service_price , 2):round($service_price , 2);//price of the service depending of there is a tva or not
        // $price = $payment>=$detail_price?$detail_price:$payment;
        $devis_id=$selected_detail['id_devis'];
        $dossier_id =$dossiers_id[$index];
        $servicePaymentDetails=getPaymentDetails($dev_id);
        $brokerchecking = checkBroker_devis($devis_id);
        if($brokerchecking>0 && $payment+$servicePaymentDetails['avanceSum']>=$detail_price){
            $service_UI=$services_UId[$index];
            $brokerdetails= getBrokerdetails($service_UI,$devis_id);
            $brokerSrvP=($brokerdetails['new_discount']===0)?$brokerdetails['new_prix']:$brokerdetails['new_prix']-(($brokerdetails['new_prix']*$brokerdetails['new_discount'])/100);
            $broker_commission= $detail_price - $brokerSrvP;
        }else{
            $broker_commission=0;
        }
        if(!is_null($servicePaymentDetails )&& $servicePaymentDetails['avanceSum']>0){
            if($payment >= $detail_price){
                $priceLeft =$detail_price - $servicePaymentDetails['avanceSum'];
                $payment=$payment - $priceLeft;
                $montant_paye =$priceLeft;
                payDevis($dev_id,$payment_method,$devis_id,$payment_giver,$dossier_id,$detail_price,$montant_paye,$broker_commission);
                if ($index+1 == count($allCheckedServicesId)){    //if this is the last service and still have $payment
                    header("Location: payments.php?message=" . urlencode($message));
                    exit();
                }
            }else if($payment < $detail_price){
                if($payment==0){
                 // -----success mesaage
                header("Location: payments.php?message=" . urlencode($message));
                exit();
                }
                $avanceSum= $payment +$servicePaymentDetails['avanceSum'];
                if($avanceSum<=$detail_price){
                    $montant_paye= $payment;
                    payDevis($dev_id,$payment_method,$devis_id,$payment_giver,$dossier_id,$detail_price,$montant_paye,$broker_commission);
                 // -----success mesaage
                header("Location: payments.php?message=" . urlencode($message));
                exit();
                }
                $priceLeft =$detail_price - $servicePaymentDetails['avanceSum'];
                $payment =$payment -$priceLeft;
                $montant_paye = $priceLeft;  
                payDevis($dev_id,$payment_method,$devis_id,$payment_giver,$dossier_id,$detail_price,$montant_paye,$broker_commission);
                if ($index+1 == count($allCheckedServicesId)){    //if this is the last service and still have $payment
                    header("Location: payments.php?message=" . urlencode($message));
                    exit();
                }
            }
        }else{
            if($payment >= $detail_price){
                $payment=$payment-$detail_price;
                $montant_paye=$detail_price;
                payDevis($dev_id,$payment_method,$devis_id,$payment_giver,$dossier_id,$detail_price,$montant_paye,$broker_commission);
                if($payment==0 || $index+1 == count($allCheckedServicesId)){    //this when we are in the last checked service and pyament =0 or still has amount
                    header("Location: payments.php?message=" . urlencode($message));
                    exit();
                }
            }else if($payment < $detail_price){
                if($payment==0){
                header("Location: payments.php?message=" . urlencode($message));
                exit();
                }
                $montant_paye = $payment;  
                payDevis($dev_id,$payment_method,$devis_id,$payment_giver,$dossier_id,$detail_price,$montant_paye,$broker_commission);
                 // -----success mesaage
                header("Location: payments.php?message=" . urlencode($message));
                exit();
            }
        }             
    }
}elseif($_POST){
    //select the devis id of the services depending on detai_devis.id and pay them
    $dossiers_id = $_POST["Dossiers"];
    $allDisplayedServicesId = $_POST["servicesId"];
    $services_UId =$_POST["uniqueIds"];
    foreach($allDisplayedServicesId as $index =>$dev_id){
        $query = "SELECT detail_devis.*, dossier.N_dossier
        FROM detail_devis
        LEFT JOIN dossier ON detail_devis.id = dossier.id_service
        WHERE detail_devis.id = '$dev_id'";
        $res =mysqli_query($cnx,$query);
        $row = mysqli_fetch_assoc($res);
        $selected_detail = getDetailDevisById($dev_id); //=> "SELECT * FROM `detail_devis` WHERE `id`='$dev_id'
        // $service_price = $selected_detail['prix']; // price of the service
        $service_price =($selected_detail['discount']==0)? $selected_detail['prix']:$selected_detail['prix']-(($selected_detail['prix']*$selected_detail['discount'])/100);
        $devisRow = getDevisById($row['id_devis']); //=> SELECT * FROM `devis` WHERE `id`='$id';
        $detail_price = ($devisRow['remove_tva'] != 1) ? round(($service_price*0.2) + $service_price , 2) : round($service_price , 2);//price of the service depending of there is a tva or not
        // $price = $payment>=$detail_price?$detail_price:$payment;
        $devis_id=$selected_detail['id_devis'];
        $dossier_id =$dossiers_id[$index];
        $servicePaymentDetails=getPaymentDetails($dev_id);
        $brokerchecking = checkBroker_devis($devis_id);
        if($brokerchecking>0 && $payment+$servicePaymentDetails['avanceSum']>=$detail_price){
            $service_UI=$services_UId[$index];
            $brokerdetails= getBrokerdetails($service_UI,$devis_id);
            $brokerSrvP=($brokerdetails['new_discount']===0)?$brokerdetails['new_prix']:$brokerdetails['new_prix']-(($brokerdetails['new_prix']*$brokerdetails['new_discount'])/100);
            $broker_commission= $detail_price - $brokerSrvP;
        }else{
            $broker_commission=0;
        }
        if(!is_null($servicePaymentDetails )&& $servicePaymentDetails['avanceSum']>0){
            if($payment >= $detail_price){
                $priceLeft =$detail_price - $servicePaymentDetails['avanceSum'];
                $payment=$payment - $priceLeft;
                $montant_paye =$priceLeft;
                payDevis($dev_id,$payment_method,$devis_id,$payment_giver,$dossier_id,$detail_price,$montant_paye,$broker_commission);
                if ($index+1 == count($allDisplayedServicesId)){    //if this is the last service and still have $payment
                    header("Location: payments.php?message=" . urlencode($message));
                    exit();
                }
            }else if($payment < $detail_price){
                if($payment==0){
                    // -----success mesaage
                    header("Location: payments.php?message=" . urlencode($message));
                    exit();
                }
                $avanceSum= $payment +$servicePaymentDetails['avanceSum'];
                if($avanceSum<=$detail_price){
                    $montant_paye= $payment;
                    payDevis($dev_id,$payment_method,$devis_id,$payment_giver,$dossier_id,$detail_price,$montant_paye,$broker_commission);
                 // -----success mesaage
                header("Location: payments.php?message=" . urlencode($message));
                exit();
                }
                $priceLeft =$detail_price - $servicePaymentDetails['avanceSum'];
                $payment =$payment -$priceLeft;
                $montant_paye = $priceLeft;  
                payDevis($dev_id,$payment_method,$devis_id,$payment_giver,$dossier_id,$detail_price,$montant_paye,$broker_commission);
                if ($index+1 == count($allDisplayedServicesId)){    //if this is the last service and still have $payment
                    header("Location: payments.php?message=" . urlencode($message));
                    exit();
                }
            }
        }else{
            if($payment >= $detail_price){
                $payment=$payment-$detail_price;
                $montant_paye=$detail_price;
                payDevis($dev_id,$payment_method,$devis_id,$payment_giver,$dossier_id,$detail_price,$montant_paye,$broker_commission);
                if ($index+1 == count($allDisplayedServicesId)){    //if this is the last service and still have $payment
                    header("Location: payments.php?message=" . urlencode($message));
                    exit();
                }
            }else if($payment < $detail_price){
                if($payment==0){
                 // -----success mesaage
                header("Location: payments.php?message=" . urlencode($message));
                exit();
                }
                $montant_paye = $payment;  
                payDevis($dev_id,$payment_method,$devis_id,$payment_giver,$dossier_id,$detail_price,$montant_paye,$broker_commission);
                 // -----success mesaage
                header("Location: payments.php?message=" . urlencode($message));
                exit();
            }
        }
    }
}

?>