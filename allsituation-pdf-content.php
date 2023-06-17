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
        table, th, tr, td {
            border: 1px solid black;
            text-align: center;
            padding: 5px;
        }
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
        .text-center{
            text-align: center;
        }
        .underline {
            text-decoration: underline;
        }
        ul li {
            list-style: none;
        }
        .break_page{
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    <?php
            $query = "SELECT d.id, d.id_client, d.number, d.remove_tva, dd.ref, dd.service_name, dossier.date as date_creation, dd.prix, dd.discount,
            CASE
                WHEN c.type = 'individual' THEN (SELECT CONCAT(ci.prenom, ' ', UPPER(ci.nom)) AS Client FROM client_individual ci WHERE c.id_client = ci.id)
                WHEN c.type = 'entreprise' THEN (SELECT UPPER(ce.nom) FROM client_entreprise ce WHERE c.id_client = ce.id)
            END AS client,
            d.objet, COALESCE(dp.total_montant_paye, 0) AS total_montant_paye, dossier.N_dossier
            FROM devis d
            INNER JOIN client c ON d.id_client = c.id
            INNER JOIN detail_devis dd ON d.id = dd.id_devis
            LEFT JOIN dossier ON dd.id = dossier.id_service
            LEFT JOIN (
                SELECT id_devis, prix, SUM(montant_paye) AS total_montant_paye
                FROM devis_payments
                GROUP BY id_devis
            ) dp ON dd.id = dp.id_devis
            WHERE d.remove = 0 AND dd.confirmed = 1";
            $request ="SELECT  receipt.R_number,receipt.id_payment,Sum(devis_payments.montant_paye) as receiptMontant,devis_payments.pay_method, receipt.date
            FROM receipt 
            LEFT JOIN devis_payments ON  receipt.id_payment=devis_payments.id 
            INNER JOIN detail_devis dd ON devis_payments.id_devis = dd.id
            LEFT JOIN  devis d ON dd.id_devis =d.id
            LEFT JOIN dossier ON dd.id = dossier.id_service
            LEFT JOIN (
                SELECT id_devis, prix, SUM(montant_paye) AS total_montant_paye
                FROM devis_payments
                GROUP BY id_devis
            ) dp ON dd.id = dp.id_devis
            WHERE d.remove = 0 AND dd.confirmed=1";
            $conditions2=[];
            $conditions = [];
            if (isset($_GET["pd_st"])) {
                $paid_status = $_GET["pd_st"];
                $conditions[] = "(
                    ($paid_status = 0 AND COALESCE(dp.total_montant_paye, 0) = 0) OR
                    ($paid_status = 3 AND dd.prix = 0) OR
                    ($paid_status = 1 AND COALESCE(dp.total_montant_paye, 0) = dp.prix) OR
                    ($paid_status = 2 AND COALESCE(dp.total_montant_paye, 0) > 0 AND COALESCE(dp.total_montant_paye, 0) < dp.prix)
                )";
                $conditions2[] = "(
                    ($paid_status = 0 AND COALESCE(dp.total_montant_paye, 0) = 0) OR
                    ($paid_status = 3 AND dd.prix = 0) OR
                    ($paid_status = 1 AND COALESCE(dp.total_montant_paye, 0) = dp.prix) OR
                    ($paid_status = 2 AND COALESCE(dp.total_montant_paye, 0) > 0 AND COALESCE(dp.total_montant_paye, 0) < dp.prix)
                )";
            }

            if (isset($_GET['sl_y'])) {
                $sl_y = $_GET["sl_y"];
                $conditions[] = "(
            (YEAR(dossier.date) = $sl_y AND dossier.date IS NOT NULL) OR
                    (YEAR(d.date_creation) = $sl_y AND dossier.date IS NULL)
                )";
                $conditions2[] = "(
            (YEAR(dossier.date) = $sl_y AND dossier.date IS NOT NULL) OR
                    (YEAR(d.date_creation) = $sl_y AND dossier.date IS NULL)
                )";
            }

            if (isset($_GET['sl_m'])) {
                $sl_m = $_GET["sl_m"];
                $conditions[] = "(
                    (MONTH(dossier.date) = $sl_m AND dossier.date IS NOT NULL) OR
                    (MONTH(d.date_creation) = $sl_m AND dossier.date IS NULL)
                )";
                $conditions2[] = "(
                    (MONTH(dossier.date) = $sl_m AND dossier.date IS NOT NULL) OR
                    (MONTH(d.date_creation) = $sl_m AND dossier.date IS NULL)
                )";
            }

            if (isset($_GET['cl_id'])) {
                $cl_id = $_GET["cl_id"];
                $conditions[] = " d.id_client = $cl_id ";
                $conditions2[] = " d.id_client = $cl_id ";
                $Situation_number = addSituation($cl_id);
            }

            if (isset($_GET['srv_name'])) {
                $srv_name = mysqli_real_escape_string($cnx, str_replace("%20", " ", $_GET["srv_name"]));
                $conditions[] = "dd.service_name = '$srv_name'";
                $conditions2[] = "dd.service_name = '$srv_name'";
            }

            if (!empty($conditions)) {
                $query .= " AND " . implode(" AND ", $conditions) . " ORDER BY dd.service_name;";
            }else{
                $query .= " ORDER BY dd.service_name;";
            }
            if (!empty($conditions2)) {
                $request .= " AND " . implode(" AND ", $conditions2) . " GROUP BY receipt.R_number ORDER BY receipt.date;";
            }else{
                $request .= " GROUP BY receipt.R_number ORDER BY receipt.date;";
            }
            // $Situation_number = 0;
            $RPresult = mysqli_query($cnx,$request);
            $RProws = mysqli_fetch_all($RPresult);
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
                    <span style="text-decoration:underline"><?= isset($_GET['cl_id'])==true?strtoupper($data_rows[0][9]) : strtoupper("beplan"); ?></span><br>
                    <span style="text-decoration:underline"><?= isset($_GET['cl_id'])==true?'Situation N° '.$Situation_number:"";?></span>
                </div>
            </div>
        </section>
        <section class="">
                    <?php
                    $num = 1;
                    $num2 = 1;
                    $totalPrice = 0;
                    $totalAdvance = 0;
                    $totalRemain = 0;
                    $prevRef = '';
                    $html = ''; // Initialize the variable
                    $AlltotalPrice=[];
                    $Alltotalavance=[];
                    $AlltotalRest=[];
                    $AlltotalPriceTT=0;
                    $AlltotalavanceTT=0;
                    $AlltotalRestTT=0;
                    $allservices=[];
                    $allRef=[];
                    foreach($data_rows as $row){
                        $fprice = $row[3] == '0'? $row[7] * 1.2 : $row[7];
                        $lprice = $fprice - ($fprice * ($row[8]) / 100);
                        if($row[11] == 0){$status = 'Non Payé';}
                        elseif($row[11] < $lprice && $row[11] != 0){$status = 'Avance';}
                        else{$status = 'Payé';}
                        if(floatval($row[7])==0){$status = 'Gratuit';}
                        $Dossier = $row[12]==NULL? '-':$row[12];
                        if($row[6]!=null){
                            $date = new DateTime($row[6]);
                            $formated_date = $date->format('d/m/Y');
                        }else{
                            $formated_date='-';
                        }
                        // Check if the reference is different from the previous row
                        if($row[4] !== $prevRef){
                            array_push($AlltotalPrice,$totalPrice);
                            array_push($Alltotalavance,$totalAdvance);
                            array_push($AlltotalRest,$totalRemain);
                            array_push($allservices,$row[5]);
                            array_push($allRef,$row[4]);
                            // Close the previous table if it exists and add the total row
                            if($prevRef !== ''){
                                $html .= '<tr>';
                                $html .= '<td colspan="5" style="text-align:center">TOTAUX</td>';
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
                            $html .= '<table class="table table-bordered break_page" >';
                            $html .= '<tr class="text-center text-bold">';
                            $html .= '<td colspan="4">Factures</td>';
                            $html .= '<td rowspan="2" class="align-middle" >Maitre D\'ouvrage</td>';
                            $html .= '<td colspan="4">Montants</td>';
                            $html .= '</tr>';
                            $html .= '<tr class="text-bold">';
                            $html .= '<td>N°</td>';
                            $html .= '<td>Date</td>';
                            $html .= '<td>Devis N°</td>';
                            $html .= '<td>Dossier N°</td>';
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
                        $html .= '<td>'.$row[2].'</td>';  //Devis N
                        $html .= '<td>'.$Dossier.'</td>';  //Dossier N
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
                        array_push($AlltotalPrice,$totalPrice);
                        array_push($Alltotalavance,$totalAdvance);
                        array_push($AlltotalRest,$totalRemain);
                        $html .= '<tr>';
                        $html .= '<td colspan="5" style="text-align:center">TOTAUX</td>';
                        $html .= '<td class="text-bold" style="text-align:center">'.sprintf('%05.2f',round($totalPrice,2)).'</td>';
                        $html .= '<td></td>';
                        $html .= '<td class="text-bold" style="text-align:center">'.sprintf('%05.2f',round($totalAdvance,2)).'</td>';
                        $html .= '<td class="text-bold" style="text-align:center">'.sprintf('%05.2f',round($totalRemain,2)).'</td>';
                        $html .= '</tr>';
                        $html .= '</table>';
                        //ETAT DE SOLD
                        $html .= '<h3 class="text-center underline" style="color:red">ETAT DE SOLD</h3>';
                        $html .= '<table class="table table-bordered break_page" >';
                        $html .= '<tr class=" text-bold">';
                        $html .= '<td rowspan="2">N°</td>';
                        $html .= '<td rowspan="2">Ref</td>';
                        $html .= '<td rowspan="2" class="align-middle" >Services</td>';
                        $html .= '<td colspan="4">Montants</td>';
                        $html .= '</tr>';
                        $html .= '<tr class="text-bold">';
                        $html .= '<td>Prix</td>';
                        $html .= '<td>Régle</td>';
                        $html .= '<td>payé</td>';
                        $html .= '<td>Réste</td>';
                        $html .= '</tr>';
                        for($i=1 ; $i<count($AlltotalPrice);$i++){
                            if($AlltotalPrice[$i]!=0){
                                if($AlltotalPrice[$i]==$Alltotalavance[$i]){
                                    $status='Payé';
                                }else{
                                    $status='Non Payé';
                                }
                                $html .= '<tr>';
                                $html .= '<td>'.$num2++.'</td>';     //NUMBER
                                $html .= '<td>'.$allRef[$i-1].'</td>'; //DATE 
                                $html .= '<td>'.$allservices[$i-1].'</td>'; //CLIENT
                                $html .= '<td>'.sprintf('%05.2f',round($AlltotalPrice[$i],2)).'</td>'; //PRIX
                                $html .= '<td>'.$status.'</td>';  //STATUS
                                $html .= '<td>'.sprintf('%05.2f',round($Alltotalavance[$i],2)).'</td>'; //PEYE
                                $html .= '<td>'.sprintf('%05.2f',round($AlltotalRest[$i],2)).'</td>'; //REST
                                $html .= '</tr>';
                            }
                            $AlltotalPriceTT+=$AlltotalPrice[$i];
                            $AlltotalavanceTT+=$Alltotalavance[$i];
                            $AlltotalRestTT+=$AlltotalRest[$i];
                        }
                        $html .= '<tr>';
                        $html .= '<td colspan="3" style="text-align:center">TOTAUX</td>';
                        $html .= '<td class="text-bold" style="text-align:center">'.sprintf('%05.2f',round($AlltotalPriceTT,2)).'</td>';
                        $html .= '<td></td>';
                        $html .= '<td class="text-bold" style="text-align:center">'.sprintf('%05.2f',round($AlltotalavanceTT,2)).'</td>';
                        $html .= '<td class="text-bold" style="text-align:center">'.sprintf('%05.2f',round($AlltotalRestTT,2)).'</td>';
                        $html .= '</tr>';
                        $html .= '</table>';
                    }
                    //ETAT DES AVANCES
                        $TotalPaiment=0;
                        $num3=1;
                        $html .= '<h3 class="text-center underline" style="color:red">ETAT DES AVANCES</h3>';
                        $html .= '<table class="table table-bordered break_page" >';
                        $html .= '<tr class=" text-bold">';
                        $html .= '<td">N°</td>';
                        $html .= '<td>Date</td>';
                        $html .= '<td class="align-middle" >Reciept N°</td>';
                        $html .= '<td>Avances</td>';
                        $html .= '<td>Montants</td>';
                        $html .= '<td>Mode de paiment</td>';
                        $html .= '</tr>';
                        foreach($RProws as $row){
                            $date =new datetime($row[4]);
                            if($row[0]!=null){
                                $html .= '<tr class=" text-bold">';
                                $html .= '<td">'.$num3.'</td>';
                                $html .= '<td>'.$date->format('Y-m-d').'</td>';
                                $html .= '<td class="align-middle" >'.$row[0].'</td>';
                                $html .= '<td>'. ($num3 == 1 ? $num3.'er avance' : $num3.'ème avance') .'</td>';
                                $html .= '<td>'.$row[2].'</td>';
                                $html .= '<td>'.$row[3].'</td>';
                                $html .= '</tr>';
                                $TotalPaiment+=$row[2];
                                $num3++;
                            }
                        }
                        $html .= '<tr class=" text-bold">';
                        $html .= '<td" colspan="4">TOTAUX</td>';
                        $html .= '<td" colspan="2">'.$TotalPaiment.'</td>';
                        $html .= '</tr class=" text-bold">';
                        $html .= '</table>';
                        echo $html; // Output the HTML content
                        ?>
                        </table>
        </section>
    </div>
</body>

</html>