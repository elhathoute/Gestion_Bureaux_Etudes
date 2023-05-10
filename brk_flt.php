<?php
    include 'includes/config.php';
    

    if(isset($_POST)){
        $broker_id = $_POST['brokerId'];

        $query = "CALL `getAllDossierByBroker`('".$broker_id."');";
        $res= mysqli_query($cnx,$query);
    
        $data = array();
        $number = 1;
        while($row=mysqli_fetch_assoc($res)){
            
            $subarray = array();
            $subarray[] = $number++;
            $subarray[] = $row['devis_id'];
            $subarray[] = $row['client_id'];
            $subarray[] = $row['number'];
            $subarray[] = ucfirst($row['objet']);
            $subarray[] = ucfirst($row['service_name']);
            $subarray[] = $row['service_id'];
            $subarray[] = $row['date_creation'];
            $subarray[] = $row['approved'];
            $data[] = $subarray;
    
        }
    
        $output = array('data'=>$data);
    
        echo json_encode($output);
    }



?>