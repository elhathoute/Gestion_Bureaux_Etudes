<?php
include "includes/config.php";
include "./functions.php";

$brokerId = $_POST["broker_id"];
$query = "SELECT client.id, client_entreprise.nom, NULL AS prenom
FROM broker_devis
JOIN devis ON broker_devis.id_devis = devis.id
JOIN client ON devis.id_client = client.id
JOIN client_entreprise ON client.id_client = client_entreprise.id
WHERE broker_devis.id_broker = $brokerId

UNION

SELECT client.id, client_individual.nom, client_individual.prenom
FROM broker_devis
JOIN devis ON broker_devis.id_devis = devis.id
JOIN client ON devis.id_client = client.id
JOIN client_individual ON client.id_client = client_individual.id
WHERE broker_devis.id_broker = $brokerId;";
$res = mysqli_query($cnx, $query);
$client = array();
while ($row = mysqli_fetch_assoc($res)) {
    $subarray = array();
    $subarray[] = $row['id'];
    $subarray[] = $row['nom'] . '&nbsp; '. $row['prenom'];
    $client[] = $subarray;
}
$output = array('client' => $client);
echo json_encode($output);
?>
