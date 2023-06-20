<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture</title>
    <!-- <link rel="stylesheet" href="css/bootstrap.min.css"> -->
    <style>
        * {
            box-sizing: border-box;
            /* font-family: 'Times New Roman', Times, serif; */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .my-5 {
            margin-top: 2rem;
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
        table,th,td {
            border: 2px solid;
            text-align: center;
            padding: 3px;
        }
        .table {
            border-collapse: collapse;
            width: 100%;
            font-size: 0.8rem;
        }
        .text-bold {
            font-weight: bold;
        }
        .underline{
            text-decoration: underline;
        }
        .page-break{
            page-break-after: always;
        }
    </style>
</head>

<body>
    <?php
    $path1 = 'images/sgn.png';
    $type1 = pathinfo($path1, PATHINFO_EXTENSION);
    $data1 = file_get_contents($path1);
    $base64_1 = 'data:image/' . $type1 . ';base64,' . base64_encode($data1);
    $invoiceInfo = getSelectedInvoiceInfo();
    function br2nl($string)
    {
        return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
    }
    ?>
    <div class="container">
        <section>
                <div style="position:relative;">
                    <p class="" style='font-size:1.1rem;text-decoration:underline;position:absolute;top:30;right:0'>Agadir le : <?php echo date("d/m/Y"); ?></p>
                </div>
        </section>
        <section style="margin-top:4rem">
            <div class="my-5">
                <div style="margin:auto;width:fit-content;text-align:center;font-weight:600;font-size:1.3rem">
                    <span>A</span><br>
                    <span><?php 
                    if(isset($_GET['broker_id'])){
                        $broker=getBrokerById($_GET['broker_id']);
                        echo strtoupper( $broker['nom'].' '.$broker['prenom']);
                        if($broker['brokerIce']!=''){
                            echo '<br>ICE '.$broker['brokerIce'];   
                        }
                    }else{
                        echo strtoupper(getSelectedClientName());
                    }
                    ?></span><br>
                    <span style="text-decoration:underline">Facture N°<?= $invoiceInfo["F_number"]  ?></span>
                </div>
            </div>
        </section>

        <div class="my-5">
            <span style="text-decoration:underline">Objet:</span><br>
            <p style="text-align:center;padding:0 20px"><?=ucfirst($invoiceInfo["objet"]);?>. <span style="font-weight: bold !important;">Sise</span> à <span> <?= $invoiceInfo['located'] ?></span>.</p>
        </div>
        <?php 
        if($_GET){
            $offset = 0;
            $id = $_GET['id'];
            $invoiceRows = countInvoiceRows($id);
            $num = 1;
            $splitRows = ceil($invoiceRows/12);
            $html = '';
            for ($i=0; $i < $splitRows ; $i++) { 
                    $query = "SELECT * FROM `detail_invoice` WHERE `id_invoice`='$id' LIMIT $offset,12";
                    $res = mysqli_query($cnx, $query);
                    $row = mysqli_fetch_all($res);
                    $html .= '<table class="table">
                        <thead>
                            <tr>
                                <th>N°P</th>
                                <th>Désignation des ouvrages</th>
                                <th>U</th>
                                <th>Qté</th>
                                <th>P.U(Hors T.V.A)</th>
                                <th>P.T(Hors T.V.A)</th>
                            </tr>
                        </thead>
                        <tbody>';
                
                        foreach ($row as $data) {
                            $prix = floatval($data[3]) - ((floatval($data[5])/100)*floatval($data[3]));
                            $html .= '<tr>';
                                $html .= '<td>'.$num.'</td>';
                                $html .= '<td style="text-align:left">- '.$data[2].'</td>';
                                $html .= '<td>'.$data[6].'</td>';
                                $html .= '<td>'.$data[4].'</td>';
                                $html .= '<td>'.sprintf('%05.2f',round($prix,2)).'</td>';
                                $html .= '<td>'.sprintf('%05.2f', round(floatval($prix)*floatval($data[4]),2)).'</td>';
                            $html .= '</tr>';
                            $num++;
                        }                    
                // <!-- for loop bracket -->
                //foreach loop the offset take plus 6 for the limit that means retieving data will start from the $offset row
                        $offset += 12;
                        if($i+1 != $splitRows){
                            $html .= '</tbody></table>';
                            $html .= '<div class="page-break"></div>';
                            $html .= '<div style="margin-top:5rem;"></div>';
                            
                        }
                }
                // <!-- TOTALS -->
                $html .= '<tr class="text-bold">
                    <td colspan="5">TOTAL H.T</td>
                    <td>'.$invoiceInfo["sub_total"].'</td>
                </tr>';
                $TVA=floatval($invoiceInfo['sub_total'])*0.2;
                $totalTVA=$invoiceInfo['sub_total'] + $TVA;
                    // if($invoiceInfo['remove_tva']=="0"){
                        $html .= '
                            <tr class="text-bold">
                                <td colspan="5">TVA 20%</td>
                                <td>'. sprintf('%05.2f', round(floatval($invoiceInfo['sub_total'])*0.2,2)).'</td>
                            </tr>
                            <tr class="text-bold">
                                <td colspan="5">TOTAL T.T.C</td>
                                <td>'.$totalTVA.'</td>
                            </tr>';
                    // }


                    $html .= '</tbody>
                            </table>';
                    echo $html;
                ?>
        <div class="my-3">
            <span>Arrêté la présent Facture à la somme de:</span><br>
            <p class="underline"style="text-align:center;padding:0 20px;font-size:0.9rem;"><strong><?php echo intergerIntoFrenchWords($invoiceInfo['net_total']); ?> <span><?php if(($invoiceInfo['remove_tva']=="0")) {echo('T.T.C.');}else{ echo 'H.T.';} ?></span></strong></p>
        </div>
        <div class="my-5"style="font-size:0.7rem;line-height:0.5;">
            <span class='underline'><strong>Condition de vente:</strong></span>
            <p>Ce Facture est valable pour une durée de <strong class="underline">30 jour</strong>.</p>
            <p>Veuillez nous retourner le Facture signé et précédé de la montion : <strong class="underline">Bon pour accorder et commande ,Date</strong> et <strong class="underline">Sigature.</strong></p>
            <p>Ce Facture sera considéré comme <strong class="underline">Bon de commande</strong> une fois qu'il nous sera retourné signé et cacheté par vous soins.</p>
            <p>Toute modification équivant à une <strong class="underline">annulation</strong> de ce Facture.</p>
            <p><strong class="underline">Condition de règlement :</strong></p>
            <p>Acompte de 90% au début des travaux.</p>
            <p>Acompte de 10% à la livraison des travaux</p>
            <p style="float:right;margin-right:10px;font-size:0.9rem">Signé le Directeur général</p><br>
            <img src="<?php echo $base64_1 ?>" width="240" style="margin-left:350px; margin-top:50px;"/>
        </div><br><br><br><br>
        <!-- <section >
            <div style="text-align:right;font-size:0.7rem;position:fixed;bottom:0;right:0;line-height:0;">
                <p>Email: Contact@beplan.ma / bet.beplan@gmail.com</p>
                <p>SARL au Capital de 100.000 DH - R.0 : 32605 Agadir</p>
                <p>TP : 67518958 - IF :18800084 - ICE : 001669517000008 - CNSS : 5782322</p>
                <p>Siége social : 2éme étage N`DB 208 Technopole -II- Agadir bay Cité Fonty Agadir</p>
                <p>GSM 1 : +212 661 896100 / GSM 2 : +212 662 517 038 - Fix : +212 528 228 692 / Fax : +212 528 233 706</p>
                <p>N'de compte : 022 010 0 00421 00 321699 85 25 . SGMB AG . Agadir 11 janvier N°113 Cité Dakhla Agadir</p>
            </div>
        </section> -->


        <?php } ?>



    </div>



</body>

</html>