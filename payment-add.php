<?php
  include_once 'includes/config.php';
  include_once 'functions.php';

if($_POST){
    // montant entrer par utilisateur
    $montant = $_POST['paymentClientPrice'];

    // payment by client 
        // client not select any of sercics(by defaults it select all)
            // get id of client
                if(!empty($_POST['clientId'])){
                    $client_id = $_POST['clientId'];
                    // get client by id
                    $clientServices = getAllServicesByClient($client_id);
                    // foreach($clientServices as $key=>$service){
                    //     var_dump($service);
                        allocatePayment($cnx,$clientServices, $montant);
                    // }
                    // echo "montant is $montant";
                    // var_dump($clientServices);
                }







}
// payment method

// Payment allocation function
function allocatePayment($cnx,$clientServices, $montant)
{
   
        // Iterate through the detail_devis records
        while ($clientService = mysqli_fetch_assoc($clientServices)) {
            // die(var_dump($clientService));
            $service_id = $clientService['id'];
            $id_devis=$clientService['id_devis'];
            $service_prix = $clientService['prix'];

            $user_id =$_SESSION['user_id'];
            $current_date = date('Y-m-d H:i:s');
            // get all services 
            $query2="SELECT DISTINCT id_service FROM devis_payments";
            $result2=mysqli_query($cnx,$query2);

            if($result2->num_rows>0) {
                $all_services=mysqli_fetch_all($result2)[0];
            }
            //  var_dump();
            // die(var_dump($all_services));
            $query3 = "SELECT SUM(prix) as sum_price FROM `devis_payments` WHERE id_service=$service_id LIMIT 1";
            $result3=mysqli_query($cnx,$query3);
            if (mysqli_num_rows($result3) > 0) {
                $sum_prix = mysqli_fetch_all($result3)[0];
            } else {
                $sum_prix = 0;  // Set the sum to 0 if no rows exist in the table
            }
            // die(var_dump($price));
       
            if ( ($montant <= 0) ) {
                
                break;

            }
            else{
            //  montant sup ou egal prix_service
            //  if ($service_prix <= $montant) {
                
            //     $srvAvance = $service_prix;
            //     $montant -= $service_prix;
            // }
            if($sum_prix[0] ==0 && $service_prix <= $montant){
                $srvAvance = $service_prix;
                $montant -= $service_prix;
                }
           else if($montant+$sum_prix[0] > $service_prix){
                $srvAvance = $montant - $sum_prix[0];
                $montant-=$srvAvance;

            }else{
                // die('hi');

                // montant moins que prix de service
                $srvAvance = $montant;
                $montant = 0;
            }
            // die($sum_prix[0]+$srvAvance .'='.$service_prix);
            
            if(isset($all_services) &&(in_array($service_id,$all_services))&&($sum_prix[0]+$srvAvance > $service_prix)){
                // pay next service
                // replace montant
                $montant = $srvAvance;

                   // insert into table payment
                   $updateSql = "INSERT INTO devis_payments (id , devis_id, id_service, prix, pay_method, user_id, pay_date, pending ,prix_fixe) 
                   VALUES ('',$id_devis,$service_id,$srvAvance,'',$user_id,'$current_date','',$service_prix)";
                   $result=mysqli_query($cnx,$updateSql);
                //    I HAVE ERROR IN PAYMENT IN AVANCE 
                continue;

            }
         
                
                // insert into table payment
                $updateSql = "INSERT INTO devis_payments (id , devis_id, id_service, prix, pay_method, user_id, pay_date, pending ,prix_fixe) 
                VALUES ('',$id_devis,$service_id,$srvAvance,'',$user_id,'$current_date','',$service_prix)";
                $result=mysqli_query($cnx,$updateSql);
            


         
                
            }
    
        }

       
    
}



?>