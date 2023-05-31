<?php
include "includes/config.php";

if(isset($_POST["clientID"])){
    $clientId = $_POST["clientID"];
    $query = "SELECT `id`, `number`, `id_client`, `date_creation`, `objet` FROM `devis` WHERE `id_client`= '$clientId' AND `remove`=0 AND `client_approve`=0 AND `type`='Approved';";
    $res = mysqli_query($cnx,$query);    
    $data = array();
    $number = 1;
    while($row=mysqli_fetch_assoc($res)){
        $subarray = array();
        $subarray[] = $number;
        $subarray[] = $row['number'];
        $subarray[] = $row['objet'];
        $subarray[] = $row['date_creation'];
        $subarray[] = '<button  class="btn btn-primary btn-sm" id="dossierShowServ"><span><i class="bi bi-plus-circle"></i></span></button>';
        $subarray[] = '<a target="_blank" href="devis_export.php?id='.$row['id'].'&client_id='.$clientId.'" class="btn btn-secondary ms-1 btn-sm" title="Afficher Devis" ><span><i class="bi bi-eye"></i></span></a>';
        $subarray[] = $row['id'];
        $data[] = $subarray;
        $number++;
    }
}elseif(isset($_POST["BrokerId"])){
    $BrokerId=$_POST["BrokerId"];
    $query = "SELECT devis.id,devis.number,devis.id_client ,devis.date_creation,devis.objet FROM `devis` JOIN broker_devis ON devis.id = broker_devis.id_devis WHERE broker_devis.id_broker = '$BrokerId' AND `remove`=0 AND `client_approve`=0 AND `type`='Approved';";
    $res = mysqli_query($cnx,$query);
    $data = array();
    $number = 1;
    while($row=mysqli_fetch_assoc($res)){
        $clientId = $row['id_client'];
        $subarray = array();
        $subarray[] = $number;
        $subarray[] = $row['number'];
        $subarray[] = $row['objet'];
        $subarray[] = $row['date_creation'];
        $subarray[] = '<button  class="btn btn-primary btn-sm" id="dossierShowServ"><span><i class="bi bi-plus-circle"></i></span></button>';
        $subarray[] = '<a target="_blank" href="devis_export.php?id='.$row['id'].'&client_id='.$clientId.'" class="btn btn-secondary ms-1 btn-sm" title="Afficher Devis" ><span><i class="bi bi-eye"></i></span></a>';
        $subarray[] = $row['id'];
        $data[] = $subarray;
        $number++;
    }
}

$output = array('data'=>$data);

echo json_encode($output);


?>