<?php
    include 'includes/config.php';
    include 'functions.php';


    if($_POST){
            // print_r($_POST);
            if(!empty($_POST["clientId"])){
                
                function pay($ids_array){
                    //secure connection to database
                    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
                    if(mysqli_connect_errno()){
                        echo "Failed to connect to MySQL: " . mysqli_connect_error();
                        exit();
                    }
                    //declare vars
                    $client_id = $_POST["clientId"];
                    $total_value = floatval($_POST["hiddenTotalValue"]);
                    $payment = floatval($_POST["paymentClientPrice"]); // The given amount
                    $payment_method = $_POST["payment-method"];
                    $payment_giver = mysqli_real_escape_string($cnx,$_POST['payment_giver']);
                    $filter_type = $_POST['filter_type'];

                    /**
                     * Check for Supplier Presence
                    */

                    if(isset($_POST['supplierCheckbox']) &&
                        $_POST['supplier'] != '' &&
                        $_POST['paymentSupplier'] != '' &&
                        is_numeric($_POST['paymentSupplier'])
                      )
                    {
                        $supplier_id = $_POST['supplier'];
                        $supplier = getSupplierById($supplier_id);
                        $category = getSuppCatById($supplier['cat_id']);
                        if($category['type'] == 'Bureau de controle'){
                            $amount = $_POST['paymentSupplier'] + $supplier['sold'];
                            updateSupplierSold($supplier_id,$amount);
                        }
                    }



                    //while ghadi tkon hna ....
                    while( $payment>0){
                        $devis_res = getFirstPayDevis($client_id);//invoice res
                        $devisRow = mysqli_fetch_assoc($devis_res);// invoice row
                        $devis_detail_id_arr = getDevisDetails($devisRow);
                        $broker_devis_prices = 0;
                        if(mysqli_num_rows($devis_res)!=0){
                        // foreach ($devis_detail_id_arr as $detail_id) {
                            // }
                            for ($i=0; $i < count($devis_detail_id_arr); $i++) { 
                                
                                
                                // foreach($invoice_id as $inv_id) {
                                    //checking if the detail id is in array or not
                                if( in_array($devis_detail_id_arr[$i],$ids_array)){
                                    
                                    $selected_detail = getDetailDevisById($devis_detail_id_arr[$i]);
                                    $d_price = $selected_detail['prix'];
                                    // if($devisRow['remove_tva'] != 1){
                                    //     $detail_price = ($d_price*0.2) + $d_price;
                                    // }
                                    $detail_price = ($devisRow['remove_tva'] != 1) ? round(($d_price*0.2) + $d_price,2) : round($d_price,2);
                                    $inv_total = floatval($devisRow['net_total']);
                                    // $price = $payment - ($payment-$inv_total);
                                    //to take the nedded price from the given payment
                                    // $price = $payment>=$inv_total?$inv_total:$payment;
                                    $price = $payment>=$detail_price?$detail_price:$payment;
                                    //return payment_invoice id after inserting the row
                                    $avance = getSumDevisPrices($devis_detail_id_arr[$i]);
                                    if(($detail_price - $avance) <= $payment ){
                                        $price = $detail_price - $avance;
                                    }
                                    $pay_id = payDevis($devis_detail_id_arr[$i],$price,$payment_method);
                                    //add to receipt
                                    addReceipt($pay_id,$payment_giver);
                                    //  adding to user_invoice for history...
                                    $user_id = $_SESSION['user_id'];
                                    userDevis_history($user_id,$devisRow['id'],"Paiement Effectué");
                                    //set payment_made to true for receipt
                                    // setPay_made($devis_detail_id_arr[$i]);
                                    // if($inv_total == getSumDevisPrices($devisRow['id'])){
                                    if($detail_price == getSumDevisPrices($devis_detail_id_arr[$i])){
                                        // updateInvoicePaidStatus($devisRow['id']);
                                        updateServicePaidStatus($devis_detail_id_arr[$i]);  //update detail devis column from paid_srv 0 to 1
                                        // updateDetailAvance($devis_detail_id_arr[$i],0);
                                    }
                                    // else{
                                    //     updateDetailAvance($devis_detail_id_arr[$i],$price);
                                    // }
                                    
                                    //check if this Broker included in this devis
                                    
                                    if($filter_type == "broker" && checkBroker_devis($devisRow['id']) > 0 ){
                                        //check if selected devis is Paid
                                        $broker_devis_prices += round(getSumDevisPrices($devis_detail_id_arr[$i]),2);
                                        if($broker_devis_prices == $inv_total){
                                            $brokerRow = getBroker_devisData($devisRow['id']);
                                            $broker_sum_prices = getBroker_devisSumPrices($brokerRow['id']);
                                            $broker_devis_total = ($devisRow['remove_tva'] != 1) ? ($broker_sum_prices * 1.2) : $broker_sum_prices;
                                            $broker_sold = $inv_total - $broker_devis_total;
                                            if($broker_sold >= 0){
                                                updateBrokerSold($brokerRow['id_broker'],$broker_sold);
                                            }
                                        }
                                    }


                                    $payment -= $price;
                                    if($payment<=0){
                                        break;
                                    }
                                    // if($payment == 0){
                                    //     updateDetailAvance($devis_detail_id_arr[$i],$price);
                                    // }
                                    // else if($payment < 0 && is_numeric($devis_detail_id_arr[$i+1])){
                                    //     updateDetailAvance($devis_detail_id_arr[$i+1],$price);
                                    // }
                                }else
                                {break;}
                                
                            }
                        }
                    }
                    
                }
    
                $user_id = $_SESSION['user_id'];
    
                if(isset($_POST['ids'])){
                    $checked_ids = $_POST['ids'];
                    
                    // pay($checked_ids);
                    //this code is to excute payment if any of check boxes checked
                    $payment = floatval($_POST["paymentClientPrice"]);
                    $payment_method = mysqli_real_escape_string($cnx,$_POST["payment-method"]);;
                    $payment_giver = mysqli_real_escape_string($cnx,$_POST['payment_giver']);
                    $filter_type = $_POST['filter_type'];


                    if(isset($_POST['supplierCheckbox']) &&
                        $_POST['supplier'] != '' &&
                        $_POST['paymentSupplier'] != '' &&
                        is_numeric($_POST['paymentSupplier'])
                      )
                    {
                        $supplier_id = $_POST['supplier'];
                        $supplier = getSupplierById($supplier_id);
                        $category = getSuppCatById($supplier['cat_id']);
                        if($category['type'] == 'Bureau de controle'){
                            $amount = $_POST['paymentSupplier'] + $supplier['sold'];
                            updateSupplierSold($supplier_id,$amount);
                            unset($_POST['supplierCheckbox']);
                        }
                    }

                    while( $payment>0){
                        // $broker_devis_prices = 0;
                        foreach($checked_ids as $dev_id){
                            $query = "SELECT * FROM `detail_devis` WHERE `id`='$dev_id'";
                            $res =mysqli_query($cnx,$query);
                            $row = mysqli_fetch_assoc($res);
                                $selected_detail = getDetailDevisById($dev_id);
                                $d_price = $selected_detail['prix'];
                                // if($devisRow['remove_tva'] != 1){
                                    //     $detail_price = ($d_price*0.2) + $d_price;
                                    // }
                                $devisRow = getDevisById($row['id_devis']);
                                $detail_price = ($devisRow['remove_tva'] != 1) ? round(($d_price*0.2) + $d_price,2) : round($d_price,2);
                                $inv_total = floatval($devisRow['net_total']);
                                $price = $payment>=$detail_price?$detail_price:$payment;
                                $avance = getSumDevisPrices($dev_id);
                                if(($detail_price - $avance) <= $payment ){
                                    $price = $detail_price - $avance;
                                }
                                $pay_id = payDevis($dev_id,$price,$payment_method);
                                addReceipt($pay_id,$payment_giver);
                                $fullPrice = $price+$avance;
                                if($detail_price == getSumDevisPrices($dev_id)){
                                    updateServicePaidStatus($dev_id);
                                }


                                //check if this Broker included in this devis

                                if($filter_type == "broker" && checkBroker_devis($row['id_devis']) > 0 ){
                                    //check if selected devis is Paid
                                    // $broker_devis_prices += round(getSumDevisPrices($devis_detail_id_arr[$i]),2);
                                    $broker_devis_prices = round(getDevis_detailsSumPrices($row['id_devis']),2);
                                    if($broker_devis_prices == $inv_total){
                                        $brokerRow = getBroker_devisData($row['id_devis']);
                                        $broker_sum_prices = getBroker_devisSumPrices($brokerRow['id']);
                                        $broker_devis_total = ($devisRow['remove_tva'] != 1) ? ($broker_sum_prices * 1.2) : $broker_sum_prices;
                                        $broker_sold = $inv_total - $broker_devis_total;
                                        if($broker_sold >= 0){
                                            updateBrokerSold($brokerRow['id_broker'],$broker_sold);
                                        }
                                    }
                                }



                                $payment -= $price;
                                // if($payment <= 0 && is_numeric($devis_detail_id_arr[$i+1])){
                                //     updateDetailAvance($devis_detail_id_arr[$i+1],$price);
                                // }
                                //  adding to user_invoice for history...
                                userDevis_history($user_id,$row['id_devis'],"Paiement Effectué");
                        }
                        // setPay_made($dev_id);
                        if($payment>0){
                            break;
                        }
                    }
                    $remaining_ids = array_diff($_POST["invoiceId"],$checked_ids);
                    if($payment>0){
                        $_POST["paymentClientPrice"] = $payment;
                        pay($remaining_ids);
                        foreach ($remaining_ids as $id) {
                            acceptPayment($id);
                        }
                    }
                    //adding to notifications
                    $current_date = date('Y-m-d H:i:s');
                    foreach($checked_ids as $id){
                        $query = "INSERT INTO `notifications`(`id_document`, `date`) VALUES ('$id','$current_date')";
                        mysqli_query($cnx,$query);
                    }
                    
                    $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i>&nbsp;
                        <strong>Request has been sent.</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
    
                }else{
                    //that means check boxs not checked
                    $devis_id = $_POST["invoiceId"];//array of all the ids in table
                    
                    pay($devis_id);
                    //accept payment foreach invoice
                    foreach ($devis_id as $id) {
                        // $query = "SELECT * FROM `detail_devis` WHERE `id`='$id'";
                        // $res =mysqli_query($cnx,$query);
                        // $row = mysqli_fetch_assoc($res);
                        acceptPayment($id); // update pending from 0 to 1
                        
                    }
                }
                // $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                //         <i class="bi bi-check-circle-fill"></i>&nbsp;
                //         <strong>Invoices has been Paid Successfully.</strong>
                //         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                //     </div>';
                
            }else{
                //alert error to chose a client
            }
            
            header("location:payments.php");exit();
        }else{
            header("location:payment-create.php");exit();
        }

    
    
    
    /**
     * This section for invoice Payment in case the payment will be based on invoice 
    */

    // if($_POST){
    //     // print_r($_POST);
    //     if(!empty($_POST["clientId"])){
            
    //         function pay($ids_array){
    //             //secure connection to database
    //             $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    //             if(mysqli_connect_errno()){
    //                 echo "Failed to connect to MySQL: " . mysqli_connect_error();
    //                 exit();
    //             }
    //             //declare vars
    //             $client_id = $_POST["clientId"];
    //             $total_value = floatval($_POST["hiddenTotalValue"]);
    //             $payment = floatval($_POST["paymentClientPrice"]); // The given amount
    //             $payment_method = $_POST["payment-method"];
    //             $payment_giver = mysqli_real_escape_string($cnx,$_POST['payment_giver']);
                
    //             while( $payment>0){
    //                 $invoice_res = getFirstPayInvoice($client_id);//invoice res
    //                 $invoiceRow = mysqli_fetch_assoc($invoice_res);// invoice row
                    
    //                 if(mysqli_num_rows($invoice_res)!=0){
                
    //                     // foreach($invoice_id as $inv_id) {
    //                     if( in_array($invoiceRow['id'],$ids_array)){

    //                         $inv_total = floatval($invoiceRow['net_total']);
    //                         // $price = $payment - ($payment-$inv_total);
    //                         //to take the nedded price from the given payment
    //                         $price = $payment>=$inv_total?$inv_total:$payment;
    //                         //return payment_invoice id after inserting the row
    //                         $avance = getSumInvoicePrices($invoiceRow['id']);
    //                         if(($inv_total - $avance) <= $payment ){
    //                             $price = $inv_total - $avance;
    //                         }
    //                         $pay_id = payInvoice($invoiceRow['id'],$price,$payment_method);
    //                         //add to receipt
    //                         addReceipt($pay_id,$payment_giver);
    //                         //  adding to user_invoice for history...
    //                         $user_id = $_SESSION['user_id'];
    //                         userInvoice_history($user_id,$invoiceRow['id'],"Paiement Effectué");
    //                         if($inv_total == getSumInvoicePrices($invoiceRow['id'])){
    //                             updateInvoicePaidStatus($invoiceRow['id']);
    //                         }
    //                         $payment -= $price;
    //                     }else
    //                     {break;}
    //                 }
    //             }
                
    //         }

    //         $user_id = $_SESSION['user_id'];

    //         if(isset($_POST['ids'])){
    //             $checked_ids = $_POST['ids'];
                
    //             // pay($checked_ids);
    //             //this code is to excute payment if any of check boxes checked
    //             $payment = floatval($_POST["paymentClientPrice"]);
    //             $payment_method = $_POST["payment-method"];
    //             $payment_giver = mysqli_real_escape_string($cnx,$_POST['payment_giver']);

    //             foreach($checked_ids as $inv_id){
    //                 while( $payment>0){
    //                     $query = "SELECT * FROM `invoice` WHERE `id`='$inv_id'";
    //                     $res =mysqli_query($cnx,$query);
    //                     $row = mysqli_fetch_assoc($res);
    //                     $inv_total = floatval($row['net_total']);
    //                     $price = $payment>=$inv_total?$inv_total:$payment;
    //                     $pay_id = payInvoice($row['id'],$price,$payment_method);
    //                     addReceipt($pay_id,$payment_giver);
    //                     if($inv_total == getSumInvoicePrices($row['id'])){
    //                         updateInvoicePaidStatus($row['id']);
    //                     }
    //                     $payment -= $price;
    //                 }
                    
    //                 //  adding to user_invoice for history...
    //                 userInvoice_history($user_id,$inv_id,"Paiement Effectué");

    //             }
    //             //adding to notifications
    //             $current_date = date('Y-m-d H:i:s');
    //             foreach($checked_ids as $id){
    //                 $query = "INSERT INTO `notifications`(`id_document`, `date`) VALUES ('$id','$current_date')";
    //                 mysqli_query($cnx,$query);
    //             }
                
    //             $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
    //                 <i class="bi bi-check-circle-fill"></i>&nbsp;
    //                 <strong>Request has been sent.</strong>
    //                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    //             </div>';

    //         }else{
    //             //that means check boxs not checked
    //             $invoice_id = $_POST["invoiceId"];//array of all the ids in table
                
    //             pay($invoice_id);
    //             //accept payment foreach invoice
    //             foreach ($invoice_id as $id) {
    //                 acceptPayment($id);
                    
    //             }
    //         }
    //         // $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
    //         //         <i class="bi bi-check-circle-fill"></i>&nbsp;
    //         //         <strong>Invoices has been Paid Successfully.</strong>
    //         //         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    //         //     </div>';
            
    //     }else{
    //         //alert error to chose a client
    //     }
        
    //     header("location:payments.php");exit();
    // }else{
    //     header("location:payment-create.php");exit();
    // }

?>