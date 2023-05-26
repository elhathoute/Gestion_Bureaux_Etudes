<?php
include "includes/config.php";
include "./functions.php";

$clientId = $_POST["clientId"];
$brokerId = $_POST["broker_id"];
$query = "SELECT
devis.id,
detail_devis.id AS srv_id,
devis.number,
detail_devis.discount,
detail_devis.srv_unique_id,
client.id AS client_id,
dossier.N_dossier,
dossier.id AS id_dossier,
CASE
    WHEN COUNT(*) > 1 THEN SUM(devis_payments.montant_paye)
    ELSE MAX(devis_payments.montant_paye)
END AS total_montant_paye,
detail_devis.quantity,
CASE
    WHEN client.type = 'individual' THEN (SELECT CONCAT(client_individual.prenom, ' ', client_individual.nom) AS Client FROM client_individual WHERE client.id_client = client_individual.id)
    WHEN client.type = 'entreprise' THEN ((SELECT client_entreprise.nom FROM client_entreprise WHERE client.id_client = client_entreprise.id))
END AS client,
devis.objet,
detail_devis.service_name,
detail_devis.ref,
IF(devis.remove_tva = 0, ROUND(detail_devis.prix * 0.2 + detail_devis.prix, 2), detail_devis.prix) AS srv_prix,
IFNULL(
    (SELECT SUM(devis_payments.prix) FROM devis_payments WHERE detail_devis.id = devis_payments.id_devis AND devis_payments.pending = 0), 0
) AS solde,
dossier.dossier_prix,
dossier.dossier_avc,
dossier.dossier_status,
detail_devis.srv_avance
FROM
devis
INNER JOIN broker_devis on devis.id = broker_devis.id_devis
INNER JOIN client ON devis.id_client = client.id
INNER JOIN detail_devis ON devis.id = detail_devis.id_devis
LEFT JOIN dossier ON dossier.id_service = detail_devis.id
LEFT JOIN devis_payments ON detail_devis.id = devis_payments.id_devis
WHERE
devis.remove = 0
AND broker_devis.id_broker= $brokerId
AND client.id =$clientId
AND detail_devis.paid_srv = 0
AND detail_devis.confirmed = 1
AND (devis_payments.prix IS NULL OR devis_payments.prix != devis_payments.montant_paye)
AND detail_devis.id NOT IN (
    SELECT dd.id
    FROM detail_devis dd
    INNER JOIN devis_payments dp ON dd.id = dp.id_devis
    WHERE dd.paid_srv = 0
    GROUP BY dd.id
    HAVING SUM(dp.montant_paye) = devis_payments.prix
)
GROUP BY
devis.id,
detail_devis.id,
devis.number,
client.id,
dossier.N_dossier,
dossier.id,
detail_devis.quantity,
client,
devis.objet,
detail_devis.service_name,
detail_devis.ref,
srv_prix,
solde,
dossier.dossier_prix,
dossier.dossier_avc,
dossier.dossier_status,
detail_devis.srv_avance
ORDER BY total_montant_paye DESC, devis.date_creation";
$res = mysqli_query($cnx, $query);
$data = array();
while ($row = mysqli_fetch_assoc($res)) {
    $avance =number_format($row['total_montant_paye'], 2);
    $subarray = array();
    $subarray[] = $row['number'];
    $subarray[] = $row['client'];
    $subarray[] = ($row['N_dossier'] != NULL) ? $row['N_dossier'] : '-';
    $subarray[] = $row['service_name'];
    // $subarray[] = 'Qte=' . $row['quantity'] . ' count_dossier=' . getCountDossierService($row['srv_id']);
    $subarray[] =$row['quantity'] ;
    $subarray[] = $row['discount']== 0?$row['srv_prix']:$row['srv_prix']-(($row['srv_prix']*$row['discount'])/100);
    $subarray[] = $row['total_montant_paye']== NULL?'0.00 ':$avance;
    $subarray[] = '<input type="checkbox" name="ids[]" class="CBPaymentByClient" value="'.$row["srv_id"].'"><input type="checkbox" name="devis[]" class="DevisCheckBox d-none " value="'.$row["id"].'"><input type="checkbox" name="dossiers[]" class="DossierCheckBox d-none" value="'.$row["id_dossier"].'"><input type="hidden" name="Dossiers[]" class="" value="'.$row["id_dossier"].'"><input type="hidden" name="servicesId[]" class="" value="'.$row["srv_id"].'"><input type="hidden" name="uniqueIds[]" class="" value="'.$row["srv_unique_id"].'"><input type="checkbox" name="checkeduniqueIds[]" class="serviceUICheckBox d-none" value="'.$row["srv_unique_id"].'">';
    $data[] = $subarray;
}

$output = array('data' => $data);
echo json_encode($output);
?>
