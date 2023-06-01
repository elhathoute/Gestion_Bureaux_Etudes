<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Situation</title>
    <link rel="icon" href="images/BeplanLogo_2.png">
    <!-- <link rel="stylesheet" href="css/bootstrap.min.css"> -->
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Times New Roman', Times, serif;
            /* font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; */
        }

        .my-5 {
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .container {
            width: 100%;
            margin-right: auto;
            margin-left: auto;
            /* padding: 1.5rem; */
            /* border: 2px solid red; */
        }

        .img-container {
            width: 200px;
            height: 50px;
            float: right;
            margin-right: 50px;
        }

        .img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        table,
        th,
        tr,
        td {
            border: 1px solid black;
            text-align: center;
            padding: 5px;
        }
        /* table tbody tr:nth-last-of-type(1),
        table tbody tr:nth-last-of-type(2),
        table tbody tr td:nth-of-type(1){
            border: none;
        } */

        .table {
            border-collapse: collapse;
            width: 100%;
            font-size: 1rem;
            border-bottom: none;
            border-left: none;
        }

        .text-bold {
            font-weight: bold;
        }

        .underline {
            text-decoration: underline;
        }
        ul li {
            list-style: none;
        }
    </style>
</head>

<body>
    <?php
    //bring the code from st_info.php and retrieve invoice data with getInvoiceById() function ...
        if($_GET){
            if(isset($_GET["cl_id"])){
                $clientId = $_GET["cl_id"];
                $brokerName=NULL;
                $query = "CALL `sp_getDevisSituation`('".$clientId."');";
                if(isset($_GET["pd_st"]) && isset($_GET["srv_name"])){
                    $paid_status = $_GET["pd_st"];
                    $srv_name = str_replace("%20"," ", $_GET["srv_name"]);
                    $query = "CALL `sp_getDevisSituationBoth`('".$clientId."','".$paid_status."','".$srv_name."');";
                }elseif (isset($_GET["pd_st"])) {
                    $paid_status = $_GET["pd_st"];
                    $query = "CALL `sp_getDevisSituationStatus`('".$clientId."','".$paid_status."');";
                }elseif (isset($_GET["srv_name"])){
                    $srv_name = str_replace("%20"," ", $_GET["srv_name"]);
                    $query = "CALL `sp_getDevisSituationSrv`('".$clientId."','".$srv_name."');";
                }
                $Situation_number = addSituation($clientId);
            }elseif(isset($_GET["br_id"])){
                $brokerId = $_GET["br_id"];
                $Situation_number = addSituation($brokerId);
                $brokerName =getBrokerById($brokerId);
                $query = "SELECT d.id, d.id_client, d.number, d.remove_tva, dd.ref, dd.service_name, d.date_creation,dd.prix,dd.discount ,
                CASE
                    WHEN c.type = 'individual' THEN (SELECT CONCAT(ci.prenom, ' ', UPPER(ci.nom)) AS Client FROM client_individual ci WHERE c.id_client = ci.id)
                    WHEN c.type = 'entreprise' THEN (SELECT UPPER(ce.nom) FROM client_entreprise ce WHERE c.id_client = ce.id)
                END AS client,
                d.objet, COALESCE(dp.total_montant_paye, 0) AS total_montant_paye
                FROM devis d
                INNER JOIN client c ON d.id_client = c.id
                INNER JOIN detail_devis dd ON d.id = dd.id_devis
                INNER JOIN broker_devis bd ON d.id = bd.id_devis
                LEFT JOIN (
                    SELECT id_devis,SUM(montant_paye) AS total_montant_paye
                    FROM devis_payments
                    GROUP BY id_devis
                ) dp ON dd.id = dp.id_devis
                WHERE d.remove = 0 and bd.id_broker =$brokerId AND dd.confirmed=1
                ORDER BY dd.service_name;";
                if(isset($_GET["pd_st"]) && isset($_GET["srv_name"])){
                    $paid_status = $_GET["pd_st"];
                    $srv_name = str_replace("%20"," ", $_GET["srv_name"]);
                    $query = "SELECT d.id, d.id_client, d.number, d.remove_tva, dd.ref, dd.service_name, d.date_creation,dd.prix,dd.discount ,
                    CASE
                        WHEN c.type = 'individual' THEN (SELECT CONCAT(ci.prenom, ' ', UPPER(ci.nom)) AS Client FROM client_individual ci WHERE c.id_client = ci.id)
                        WHEN c.type = 'entreprise' THEN (SELECT UPPER(ce.nom) FROM client_entreprise ce WHERE c.id_client = ce.id)
                    END AS client,
                    d.objet, COALESCE(dp.total_montant_paye, 0) AS total_montant_paye
                    FROM devis d
                    INNER JOIN client c ON d.id_client = c.id
                    INNER JOIN detail_devis dd ON d.id = dd.id_devis
                    INNER JOIN broker_devis bd ON d.id = bd.id_devis
                    LEFT JOIN (
                        SELECT id_devis,prix,SUM(montant_paye) AS total_montant_paye
                        FROM devis_payments
                        GROUP BY id_devis
                    ) dp ON dd.id = dp.id_devis
                    WHERE d.remove = 0 AND 
                        (
                            ($paid_status = 0 AND COALESCE(dp.total_montant_paye, 0) = 0) OR
                            ($paid_status = 1 AND COALESCE(dp.total_montant_paye, 0) = dp.prix) OR
                            ($paid_status =2  AND COALESCE(dp.total_montant_paye, 0) > 0 AND COALESCE(dp.total_montant_paye, 0) < dp.prix)
                        ) AND dd.service_name = '$srv_name' AND dd.confirmed=1 AND bd.id_broker =$brokerId 
                    ORDER BY dd.service_name;";
                }elseif (isset($_GET["pd_st"])) {
                    $paid_status = $_GET["pd_st"];
                    $query = "SELECT d.id, d.id_client, d.number, d.remove_tva, dd.ref, dd.service_name, d.date_creation,dd.prix,dd.discount ,
                    CASE
                        WHEN c.type = 'individual' THEN (SELECT CONCAT(ci.prenom, ' ', UPPER(ci.nom)) AS Client FROM client_individual ci WHERE c.id_client = ci.id)
                        WHEN c.type = 'entreprise' THEN (SELECT UPPER(ce.nom) FROM client_entreprise ce WHERE c.id_client = ce.id)
                    END AS client,
                    d.objet, COALESCE(dp.total_montant_paye, 0) AS total_montant_paye
                    FROM devis d
                    INNER JOIN client c ON d.id_client = c.id
                    INNER JOIN detail_devis dd ON d.id = dd.id_devis
                    INNER JOIN broker_devis bd ON d.id = bd.id_devis
                    LEFT JOIN (
                        SELECT id_devis,prix,SUM(montant_paye) AS total_montant_paye
                        FROM devis_payments
                        GROUP BY id_devis
                    ) dp ON dd.id = dp.id_devis
                    WHERE d.remove = 0 AND 
                        (
                            ($paid_status = 0 AND COALESCE(dp.total_montant_paye, 0) = 0) OR
                            ($paid_status = 1 AND COALESCE(dp.total_montant_paye, 0) = dp.prix) OR
                            ($paid_status =2  AND COALESCE(dp.total_montant_paye, 0) > 0 AND COALESCE(dp.total_montant_paye, 0) < dp.prix)
                        )  AND dd.confirmed=1 AND bd.id_broker =$brokerId 
                    ORDER BY dd.service_name;";
                }elseif (isset($_GET["srv_name"])){
                    $srv_name = str_replace("%20"," ", $_GET["srv_name"]);
                    $query = "SELECT d.id, d.id_client, d.number, d.remove_tva, dd.ref, dd.service_name, d.date_creation,dd.prix,dd.discount ,
                    CASE
                        WHEN c.type = 'individual' THEN (SELECT CONCAT(ci.prenom, ' ', UPPER(ci.nom)) AS Client FROM client_individual ci WHERE c.id_client = ci.id)
                        WHEN c.type = 'entreprise' THEN (SELECT UPPER(ce.nom) FROM client_entreprise ce WHERE c.id_client = ce.id)
                    END AS client,
                    d.objet, COALESCE(dp.total_montant_paye, 0) AS total_montant_paye
                    FROM devis d
                    INNER JOIN client c ON d.id_client = c.id
                    INNER JOIN detail_devis dd ON d.id = dd.id_devis
                    INNER JOIN broker_devis bd ON d.id = bd.id_devis
                    LEFT JOIN (
                        SELECT id_devis,prix,SUM(montant_paye) AS total_montant_paye
                        FROM devis_payments
                        GROUP BY id_devis
                    ) dp ON dd.id = dp.id_devis
                    WHERE d.remove = 0 AND dd.service_name = '$srv_name' AND dd.confirmed=1 AND bd.id_broker =$brokerId 
                    ORDER BY dd.service_name;";
                }
            }
            // $query = "CALL `sp_getSituation`('".$clientId."');";
            $res = mysqli_query($cnx,$query);
            $data_rows = mysqli_fetch_all($res);
            $ascii_code = 65;
            $solde = 0;
            $total = 0;
            $html = '';
    ?>
    <div class="container">


        <section>
                <div style="position:relative;">
                    <p class="" style='font-size:1.1rem;text-decoration:underline;position:absolute;top:30;right:0'>Agadir le: <?php echo date("d/m/Y"); ?></p>
                </div>
        </section>
        <section style="margin-top:4rem">
            <div class="my-5">
                <div style="margin:auto;width:fit-content;text-align:center;font-weight:600;font-size:1.3rem">
                    <span>DE</span><br>
                    <!-- <span><?= strtoupper($data_rows[0][7]); ?></span><br> -->
                    <span><?= $brokerName!=NULL?'Intermédiaire'.strtoupper('<br>'.$brokerName['nom'].'  '.$brokerName['prenom']):strtoupper($data_rows[0][9]); ?></span><br>
                    <span style="text-decoration:underline">Situation N°<?=$Situation_number;?></span>
                </div>
            </div>
        </section>

        <section>

                    <!-- <table class="table table-bordered">
                    <tr class="text-center text-bold">
                        <td colspan="3">Factures</td>
                        <td rowspan="2" class="align-middle">Maitre D'ouvrage</td>
                        <td colspan="4">Montants</td>
                    </tr>
                    <tr class="text-bold">
                        <td>N°</td>
                        <td>Date</td>
                        <td>Réf</td>
                        <td>Prix</td>
                        <td>Régle</td>
                        <td>payé</td>
                        <td>Réste</td>
                    </tr> -->
                    <?php



                
                    $num = 1;
                    $totalPrice = 0;
                    $totalAdvance = 0;
                    $totalRemain = 0;
                    $prevRef = '';
                    $html = ''; // Initialize the variable

                    foreach($data_rows as $row){
                        $fprice = $row[3] == '0'? $row[7] * 1.2 : $row[7];
                        $lprice = $fprice - ($fprice * ($row[8]) / 100);
                        // $totalPrice += floatval($lprice);
                        // $totalAdvance += floatval($row[11]);
                        // $totalRemain += (floatval($lprice) - floatval($row[11]));
                        if($row[11] == 0){
                            $status = 'Non Payé';
                        } elseif($row[11] < $lprice && $row[11] != 0){
                            $status = 'Avance';
                        } else{
                            $status = 'Payé';
                        }
                        if(floatval($row[7])==0){
                            $status = 'Gratuit';
                        }
                        $date = new DateTime($row[6]);
                        $formated_date = $date->format('d/m/Y');

                        // Check if the reference is different from the previous row
                        if($row[4] !== $prevRef){
                            // Close the previous table if it exists and add the total row
                            if($prevRef !== ''){
                                $html .= '<tr>';
                                $html .= '<td colspan="4" style="text-align:center">TOTAUX</td>';
                                $html .= '<td class="text-bold" style="text-align:center">'.sprintf('%05.2f',round($totalPrice,2)).'</td>';
                                $html .= '<td></td>';
                                $html .= '<td class="text-bold" style="text-align:center">'.sprintf('%05.2f',round($totalAdvance,2)).'</td>';
                                $html .= '<td class="text-bold" style="text-align:center">'.sprintf('%05.2f',round($totalRemain,2)).'</td>';
                                $html .= '</tr>';
                                $html .= '</table>';
                                $html .= '<br>'; // Add a line break after each table
                                $totalPrice = 0;
                                $totalAdvance = 0;
                                $totalRemain = 0;
                            }
                            // Start a new table for the current reference
                            $html .= '<h3 style="text-align:right;"><u>'.$row[5].'</u></h3>';
                            $html .= '<table class="table table-bordered">';
                            $html .= '<tr class="text-center text-bold">';
                            $html .= '<td colspan="3">Factures</td>';
                            $html .= '<td rowspan="2" class="align-middle">Maitre D\'ouvrage</td>';
                            $html .= '<td colspan="4">Montants</td>';
                            $html .= '</tr>';
                            $html .= '<tr class="text-bold">';
                            $html .= '<td>N°</td>';
                            $html .= '<td>Date</td>';
                            $html .= '<td>Réf</td>';
                            $html .= '<td>Prix</td>';
                            $html .= '<td>Régle</td>';
                            $html .= '<td>payé</td>';
                            $html .= '<td>Réste</td>';
                            $html .= '</tr>';
                        }
                        $totalPrice += floatval($lprice);
                        $totalAdvance += floatval($row[11]);
                        $totalRemain += (floatval($lprice) - floatval($row[11]));
                        // Output the row for the current service
                        $html .= '<tr>';
                        $html .= '<td>'.$num++.'</td>';     //NUMBER
                        $html .= '<td>'.$formated_date.'</td>'; //DATE 
                        $html .= '<td>'.$row[4].'</td>';  //REF
                        $html .= '<td>'.$row[9].'</td>'; //CLIENT
                        $html .= '<td>'.sprintf('%05.2f',round(floatval($lprice),2)).'</td>'; //PRIX
                        $html .= '<td>'.$status.'</td>';  //STATUS
                        $html .= '<td>'.sprintf('%05.2f',round(floatval($row[11]),2)).'</td>'; //PEYE
                        $html .= '<td>'.sprintf('%05.2f',round(floatval($lprice) - floatval($row[11]),2)).'</td>'; //REST
                        $html .= '</tr>';
                        
                        $prevRef = $row[4]; // Store the current reference for comparison with the next row
                    }
                    // Close the last table if it exists and add the total row
                    if($prevRef !== ''){
                        $html .= '<tr>';
                        $html .= '<td colspan="4" style="text-align:center">TOTAUX</td>';
                        $html .= '<td class="text-bold" style="text-align:center">'.sprintf('%05.2f',round($totalPrice,2)).'</td>';
                        $html .= '<td></td>';
                        $html .= '<td class="text-bold" style="text-align:center">'.sprintf('%05.2f',round($totalAdvance,2)).'</td>';
                        $html .= '<td class="text-bold" style="text-align:center">'.sprintf('%05.2f',round($totalRemain,2)).'</td>';
                        $html .= '</tr>';
                        $html .= '</table>';
                    }
                    echo $html; // Output the HTML content
                            // $num = 1;
                            // $totalPrice = 0;
                            // $totalAdvance = 0;
                            // $totalRemain = 0;
                            // foreach($data_rows as $row){
                            //     $fprice = $row[3] == '0'? $row[7] * 1.2 : $row[7];
                            //     $lprice=$fprice -($fprice*($row[8])/100);
                            //     $totalPrice += floatval($lprice);
                            //     $totalAdvance += floatval($row[11]);
                            //     $totalRemain += (floatval($lprice) - floatval($row[11]));
                            //     if($row[11]==0){
                            //         $status='Non Payé';
                            //     }elseif($row[11]<$lprice && $row[11] != 0){
                            //         $status='Avance';
                            //     }else{
                            //         $status= 'Payé';
                            //     }
                            //     $date = new DateTime($row[6]);
                            //     $formated_date = $date->format('d/m/Y');
                            //     $html .= '<tr>';
                            //     $html .= '<td>'.$num++.'</td>';     //NUMBER
                            //     $html .= '<td>'.$formated_date.'</td>'; //DATE 
                            //     $html .= '<td>'.$row[4].'</td>';  //REF
                            //     $html .= '<td>'.$row[9].'</td>'; //CLIENT
                            //     $html .= '<td>'.sprintf('%05.2f',round(floatval($lprice),2)).'</td>'; //PRIX
                            //     $html .= '<td>'.$status.'</td>';  //STATUS
                            //     $html .= '<td>'.sprintf('%05.2f',round(floatval($row[11]),2)).'</td>'; //PEYE
                            //     $html .= '<td>'.sprintf('%05.2f',round(floatval($lprice) - floatval($row[11]),2)).'</td>'; //REST
                            //     $html .='</tr>';
                                
                            // }
                            
                            // $html .= '<tr>';
                            // $html .= '<td colspan="4" style="text-align:center">TOTAUX</td>';
                            // $html .= '<td class="text-bold" style="text-align:center">'.sprintf('%05.2f',round($totalPrice,2)).'</td>';
                            // $html .= '<td></td>';
                            // $html .= '<td class="text-bold" style="text-align:center">'.sprintf('%05.2f',round($totalAdvance,2)).'</td>';
                            // $html .= '<td class="text-bold" style="text-align:center">'.sprintf('%05.2f',round($totalRemain,2)).'</td>';

                            // echo $html;



                            /**
                         * For invoice Payment 
                        */

                        // // while($row=mysqli_fetch_assoc($res)){
                        // foreach($data_rows as $row){
                        //     $invoice_info = getInvoiceById($row[0]);
                        //         // $percentage = (floatval($row['avance']) / floatval($row['net_total']))*100;
                        //     $percentage = (floatval($row[5]) / floatval($row[4]))*100;
                        //     $detail_total = 0;
                        //     $html .= '<tr>';
                        //     $html .= '<td>'.chr($ascii_code).'/ '.$row[3].'';
                        //     $html .= '<ol type="1">';
                        //     $inv_details = getInvDetailById($row[0]);
                        //     foreach ($inv_details as $detail) {
                        //         $html .= '<li>'.ucfirst($detail[2]).'</li>';
                        //     }
                        //     $html .='</ol></td>';
                        //     $html .='<td><br>';
                        //     $html .= '<ul>';
                        //     foreach ($inv_details as $detail) {
                        //         //price of service minus discount if any and multiplied by Quantity
                        //         $service_price = (floatval($detail[3]) - ((floatval($detail[5])/100)*floatval($detail[3]))) * floatval($detail[4]);
                        //         $html .= '<li>'. sprintf('%05.2f',round($service_price,2)).' DH</li>';
                        //         $detail_total += $service_price;
                        //     }
                        //     $html .= '</ul></td>';
                        //     $html .= '<td align="center">'.ceil(round($percentage,2)).'%</td>';
                        //     $html .= '<td align="center">'.sprintf('%05.2f',round($row[5],2)).' DH</td></tr>';
                        //     //adding tva if any
                        //     if($invoice_info["remove_tva"]=='0'){
                        //         $html .= '<tr><td style="text-align:right;background-color:#ddd;padding-right:1rem;font-weight: bold;">H.T</td><td style="background-color:#ddd;padding-left:2rem">'.sprintf('%05.2f',round($detail_total,2)).' DH</td> <td colspan="2"></td></tr>';
                        //         $detail_total += $detail_total*0.2;
                        //     }
                        //     $html .= '<tr><td></td>';
                        //     $html .= '<td style="background-color:#aaa;padding-left:2rem">'.sprintf('%05.2f',round($detail_total,2)).' DH</td>';
                        //     $html .= '<td colspan="2"></td></tr>';
                            
                        //     $solde += floatval($row[5]);
                        //     $total += floatval($row[4]);
                        //     $ascii_code++;

                           


                        // }
                        // // echo $html;
                        // $html .= '<tr>
                        //         <td colspan="2"></td>
                        //         <td align="center">Total NET TTC</td>
                        //         <td align="center" style="background-color:#ddd;font-size:1.1rem">'.sprintf('%05.2f',round($total,2)).' DH</td></tr>';
                        // $html .= '<tr>
                        //     <td colspan="2"></td>
                        //     <td align="center">Reste à payer TTC</td>
                        //     <td align="center" style="background-color:#ddd;font-size:1.1rem">'.sprintf('%05.2f',round($total-$solde,2)).' DH</td></tr>';

                        // echo $html;
                        ?>
                        </table>




                    <!-- <tr>
                        <td>
                            <ol type="A">
                                <li>Facture Object</li>
                                <ol type="1">
                                    <li>service1</li>
                                    <li>service2</li>
                                    <li>service3</li>
                                </ol>
                            </ol>
                        </td>
                        <td>
                            <br>
                            <ul>
                                <li>100 DH</li>
                                <li>200 DH</li>
                                <li>400 DH</li>
                            </ul>
                        </td>
                        <td align="right">80%</td>
                        <td align="center">123.81 DH</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="background-color:#ddd;padding-left:2rem">700 DH</td>
                        <td colspan="2"></td>
                    </tr>


                    <tr>
                        <td colspan="2"></td>
                        <td align="center">Solde</td>
                        <td align="center" style="background-color:#ddd;">123DH</td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td align="center">Total</td>
                        <td align="center" style="background-color:#ddd;">1324 DH</td>
                    </tr> -->
                <!-- </tbody>
            </table> -->
        </section>
    </div>
    <?php } ?>



</body>

</html>