<?php
    include 'includes/config.php';
    

    if(isset($_POST)){
        $clientId = $_POST['clientid'];

        $query = "SELECT dossier.N_dossier,detail_devis.approved,dossier.date as dossierDate,devis.id_client AS client_id,detail_devis.id as 'service_id' ,devis.id as devis_id,client.* ,detail_devis.service_name , devis.objet
        from dossier
        LEFT JOIN detail_devis on detail_devis.id=dossier.id_service
        LEFT JOIN devis on detail_devis.id_devis=devis.id
        LEFT JOIN client on client.id=devis.id_client
        LEFT JOIN client_individual on client_individual.id=client.id_client
        LEFT JOIN client_entreprise on client_entreprise.id=client.id_client
        where devis.id_client=$clientId and client.remove='0' AND detail_devis.approved=1;";
        $res= mysqli_query($cnx,$query);
    
        $data = array();
        $number = 1;
        while($row=mysqli_fetch_assoc($res)){
            $subarray = array();
            $subarray[] = $number++;
            $subarray[] = $row['devis_id'];
            $subarray[] = $row['client_id'];
            $subarray[] = $row['N_dossier'];
            $subarray[] = ucfirst($row['objet']);
            $subarray[] = ucfirst($row['service_name']);
            $subarray[] = $row['service_id'];
            $subarray[] = $row['dossierDate'];
            $subarray[] = $row['approved'];
            $data[] = $subarray;
        }
        $output = array('data'=>$data);
    
        echo json_encode($output);
    }



?>