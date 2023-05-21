<?php
    include 'includes/config.php';
    include 'functions.php';


    if($_POST){
     
        // user check services to pay them
        if(isset($_POST['ids'])){
              // montant
             $montant = $_POST['paymentClientPrice'];
        
            // payment giver
            $giver =$_POST['payment_giver'];
            // payment method
            $method = $_POST['payment-method'];
            // les services 
            $services =array();
            $services = $_POST['ids'];
            // devis (take first element because the same devis)
            $devis_id=array();
            $devis_id=array(reset($_POST['devis']));
            // dossiers
            $dossiers=array();
            $dossiers=$_POST['dossiers'];
            // get all devis_services not payed
            // $dev_serv_not_payed = getAllDevisServicesNotPayed($devis_id[0]);
            // print_r($dev_serv_not_payed);
            // die(var_dump($montant,$client,$giver,$method,$services,$devis,$dossiers));
        }else{

                // client_id
                $client=$_POST['clientId'];
                echo die($client);
        }
      
             
        // die(var_dump($_POST));
        
        // var_dump($_POST);
        // if(!empty($_POST["clientId"])){
            // $json = json_encode($_POST, JSON_PRETTY_PRINT);
            // echo $json;
            // die(var_dump($_POST['clientId']));
                // function pay($ids_array){
                //     //secure connection to database
                //     $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
                //     if(mysqli_connect_errno()){
                //         echo "Failed to connect to MySQL: " . mysqli_connect_error();
                //         exit();
                //     }
                //     //declare vars
                //     $client_id = $_POST["clientId"];
                //     $total_value = floatval($_POST["hiddenTotalValue"]);
                //     $payment = floatval($_POST["paymentClientPrice"]); // The given amount
                //     $payment_method = $_POST["payment-method"];
                //     $payment_giver = mysqli_real_escape_string($cnx,$_POST['payment_giver']);
                //     //fourniss
                //     $filter_type = $_POST['filter_type'];

                //     /**
                //      * Check for Supplier Presence
                //     */

                //     if(isset($_POST['supplierCheckbox']) &&
                //         $_POST['supplier'] != '' &&
                //         $_POST['paymentSupplier'] != '' &&
                //         is_numeric($_POST['paymentSupplier'])
                //       )
                //     {
                //         $supplier_id = $_POST['supplier'];
                //         $supplier = getSupplierById($supplier_id);
                //         $category = getSuppCatById($supplier['cat_id']);
                //         if($category['type'] == 'Bureau de contrôle'){
                //             $amount = $_POST['paymentSupplier'] + $supplier['sold'];
                //             updateSupplierSold($supplier_id,$amount);
                //         }
                //     }



                //     //while ghadi tkon hna ....
                //     while( $payment>0){
                //         $devis_res = getFirstPayDevis($client_id);//invoice res
                //         $devisRow = mysqli_fetch_assoc($devis_res);// invoice row
                //         $devis_detail_id_arr = getDevisDetails($devisRow);
                //         $broker_devis_prices = 0;
                //         if(mysqli_num_rows($devis_res)!=0){
                    
                //             for ($i=0; $i < count($devis_detail_id_arr); $i++) { 
                               
                //                 if( in_array($devis_detail_id_arr[$i],$ids_array)){
                                    
                //                     $selected_detail = getDetailDevisById($devis_detail_id_arr[$i]);
                //                     $d_price = $selected_detail['prix'];
                                  
                //                     $detail_price = ($devisRow['remove_tva'] != 1) ? round(($d_price*0.2) + $d_price,2) : round($d_price,2);
                //                     $inv_total = floatval($devisRow['net_total']);
                                 
                //                     $price = $payment>=$detail_price?$detail_price:$payment;
                //                     //return payment_invoice id after inserting the row
                //                     $avance = getSumDevisPrices($devis_detail_id_arr[$i]);
                //                     if(($detail_price - $avance) <= $payment ){
                //                         $price = $detail_price - $avance;
                //                     }
                //                     $pay_id = payDevis($devis_detail_id_arr[$i],$price,$payment_method);
                //                     //add to receipt
                //                     addReceipt($pay_id,$payment_giver);
                //                     //  adding to user_invoice for history...
                //                     $user_id = $_SESSION['user_id'];
                //                     userDevis_history($user_id,$devisRow['id'],"Paiement Effectué");
                                   
                //                     if($detail_price == getSumDevisPrices($devis_detail_id_arr[$i])){
                //                         // updateInvoicePaidStatus($devisRow['id']);
                //                         updateServicePaidStatus($devis_detail_id_arr[$i]);
                //                         // updateDetailAvance($devis_detail_id_arr[$i],0);
                //                     }
                                 
                //                     //check if this Broker included in this devis
                                    
                //                     if($filter_type == "broker" && checkBroker_devis($devisRow['id']) > 0 ){
                //                         //check if selected devis is Paid
                //                         $broker_devis_prices += round(getSumDevisPrices($devis_detail_id_arr[$i]),2);
                //                         if($broker_devis_prices == $inv_total){
                //                             $brokerRow = getBroker_devisData($devisRow['id']);
                //                             $broker_sum_prices = getBroker_devisSumPrices($brokerRow['id']);
                //                             $broker_devis_total = ($devisRow['remove_tva'] != 1) ? ($broker_sum_prices * 1.2) : $broker_sum_prices;
                //                             $broker_sold = $inv_total - $broker_devis_total;
                //                             if($broker_sold >= 0){
                //                                 updateBrokerSold($brokerRow['id_broker'],$broker_sold);
                //                             }
                //                         }
                //                     }


                //                     $payment -= $price;
                //                     if($payment<=0){
                //                         break;
                //                     }
                                 
                //                 }else
                //                 {break;}
                                
                //             }
                //         }
                //     }
                    
                // }
    
                // $user_id = $_SESSION['user_id'];
    


                
                // if(isset($_POST['ids'])){
          
                //     $checked_ids = $_POST['ids'];
                    
                //     //this code is to excute payment if any of check boxes checked
                //     $payment = floatval($_POST["paymentClientPrice"]);
                //     $payment_method = mysqli_real_escape_string($cnx,$_POST["payment-method"]);;
                //     $payment_giver = mysqli_real_escape_string($cnx,$_POST['payment_giver']);
                //     $filter_type = $_POST['filter_type'];


                //     if(isset($_POST['supplierCheckbox']) &&
                //         $_POST['supplier'] != '' &&
                //         $_POST['paymentSupplier'] != '' &&
                //         is_numeric($_POST['paymentSupplier'])
                //       )
                //     {
                //         $supplier_id = $_POST['supplier'];
                //         $supplier = getSupplierById($supplier_id);
                //         $category = getSuppCatById($supplier['cat_id']);
                 
                //         if($category['type'] == 'Bureau de controle'){
                //             $amount = $_POST['paymentSupplier'] + $supplier['sold'];
                //             updateSupplierSold($supplier_id,$amount);
                //             unset($_POST['supplierCheckbox']);
                //         }
                //     }

                //     while( $payment>0){
                   
                //         foreach($checked_ids as $dev_id){
                //             $query = "SELECT * FROM `detail_devis` WHERE `id`='$dev_id'";
                //             $res =mysqli_query($cnx,$query);
                //             $row = mysqli_fetch_assoc($res);
                //                 $selected_detail = getDetailDevisById($dev_id);
                //                 $d_price = $selected_detail['prix'];
                               
                //                 $devisRow = getDevisById($row['id_devis']);
                //                 $detail_price = ($devisRow['remove_tva'] != 1) ? round(($d_price*0.2) + $d_price,2) : round($d_price,2);
                //                 $inv_total = floatval($devisRow['net_total']);
                //                 $price = $payment>=$detail_price?$detail_price:$payment;
                //                 $avance = getSumDevisPrices($dev_id);
                //                 if(($detail_price - $avance) <= $payment ){
                //                     $price = $detail_price - $avance;
                //                 }
                //                 $pay_id = payDevis($dev_id,$price,$payment_method);
                //                 addReceipt($pay_id,$payment_giver);
                //                 $fullPrice = $price+$avance;
                //                 if($detail_price == getSumDevisPrices($dev_id)){
                //                     updateServicePaidStatus($dev_id);
                //                 }


                //                 //check if this Broker included in this devis

                //                 if($filter_type == "broker" && checkBroker_devis($row['id_devis']) > 0 ){
                //                     //check if selected devis is Paid
                //                     $broker_devis_prices = round(getDevis_detailsSumPrices($row['id_devis']),2);
                //                     if($broker_devis_prices == $inv_total){
                //                         $brokerRow = getBroker_devisData($row['id_devis']);
                //                         $broker_sum_prices = getBroker_devisSumPrices($brokerRow['id']);
                //                         $broker_devis_total = ($devisRow['remove_tva'] != 1) ? ($broker_sum_prices * 1.2) : $broker_sum_prices;
                //                         $broker_sold = $inv_total - $broker_devis_total;
                //                         if($broker_sold >= 0){
                //                             updateBrokerSold($brokerRow['id_broker'],$broker_sold);
                //                         }
                //                     }
                //                 }



                //                 $payment -= $price;
                             
                //                 //  adding to user_invoice for history...
                //                 userDevis_history($user_id,$row['id_devis'],"Paiement Effectué");
                //         }
                //         // setPay_made($dev_id);
                //         if($payment>0){
                //             break;
                //         }
                //     }
                //     $remaining_ids = array_diff($_POST["invoiceId"],$checked_ids);
                //     if($payment>0){
                //         $_POST["paymentClientPrice"] = $payment;
                //         pay($remaining_ids);
                //         foreach ($remaining_ids as $id) {
                //             acceptPayment($id);
                //         }
                //     }
                //     //adding to notifications
                //     $current_date = date('Y-m-d H:i:s');
                //     foreach($checked_ids as $id){
                //         $query = "INSERT INTO `notifications`(`id_document`, `date`) VALUES ('$id','$current_date')";
                //         mysqli_query($cnx,$query);
                //     }
                    
                //     $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                //         <i class="bi bi-check-circle-fill"></i>&nbsp;
                //         <strong>Request has been sent.</strong>
                //         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                //     </div>';
    
                // }else{
                //     //that means check boxs not checked
                //     $devis_id = $_POST["invoiceId"];//array of all the ids in table
                    
                //     pay($devis_id);
                //     //accept payment foreach invoice
                //     foreach ($devis_id as $id) {
                //         acceptPayment($id); 
                //     }
                // }
         
            // }
            // else{
            //     //alert error to chose a client
            //     die('choose client');
            // }
            
            // header("location:payments.php");exit();
        }
        // else{
        //     header("location:payment-create.php");exit();
        // }

    

?>