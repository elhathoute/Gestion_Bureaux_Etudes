<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis</title>

    <style>
    
        * {
            box-sizing: border-box;
            /* font-family: 'Times New Roman', Times, serif; */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .my-5 {
            margin-top: 1rem;
            margin-bottom: 1rem;
        }
        /* section{} */
        .container {
            width: 100%;
            margin-right: auto;
            margin-left: auto;
        }

        

        .img-container {
            width: 200px;
            height: 70px;
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
        td {
            border: 2px solid;
            text-align: center;
            padding: 5px;
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
<!-- TODO Fix the ref in here and in invoice pdf-content -->
<body>
    <?php
    $path1 = 'images/sgn.png';
    $type1 = pathinfo($path1, PATHINFO_EXTENSION);
    $data1 = file_get_contents($path1);
    $base64_1 = 'data:image/' . $type1 . ';base64,' . base64_encode($data1);
    $invoiceInfo = getSelectedInvoiceInfo();
    $devisInfo = getSelectedDevisInfo();

    function br2nl($string)
    {
        return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
    }
    ?>
    <div class="container">
        
        <!-- <section style="border:1px solid red;margin-bottom:50px;">
            <div class="img-container" style="border:1px solid green;">
                <img src="" alt="Logo Company">
            </div>
        </section> -->

        
        
        <section>
                <div style="position:relative;">
                    <p class="" style='font-size:1rem;text-decoration:underline;position:absolute;top:30;right:0'>Agadir le : <?php echo date("d/m/Y"); ?></p>
                </div>
        </section>
        <section style="margin-top:5rem">
            <div class="my-5">
                <div style="margin:auto;width:fit-content;text-align:center;font-weight:600;font-size:1.3rem">
                    <span>A</span><br>
                    <span><?php 
                    if(isset($_GET['broker_id'])){
                        echo strtoupper( $devisInfo['nom'].' '.$devisInfo['prenom']);
                    }else{
                        echo strtoupper(getSelectedClientName());
                    }
                    ?></span><br>
                    <span style="text-decoration:underline">Devis N°<?= $devisInfo["number"]  ?></span>
                </div>
            </div>
        </section>

        <div class="my-5">
            <?php
            $MO='';
            if(isset($_GET['broker_id'])){
                $MO.='<span style="text-decoration:underline;margin-right:5px;">MO:</span><span> '.strtoupper(getSelectedClientName()).'</span><br><br>';
                echo $MO;
            }
            ?>
            <span style="text-decoration:underline">Objet:tttt</span><br>
            <p style="text-align:center;padding:0 20px"><?=$devisInfo['objet'];?>. <span style="font-weight: bold !important;">Sis</span> à <span> <?= $devisInfo['located'] ?></span>.</p>
        </div>
        <?php 
        if($_GET){ 
            $offset = 0;
            $id = $_GET['id'];
            $devisRows = countDevisRows($id);
            $num = 1;
            $splitRows = ceil($devisRows/12);
            $html = '';
            for ($i=0; $i < $splitRows ; $i++) {
                
                if(isset($_GET['broker_id'])){
                    $query = "SELECT detail_devis.id ,detail_devis.id_devis ,detail_devis.service_name ,detail_broker_devis.new_prix ,detail_devis.quantity,detail_broker_devis.new_discount,detail_devis.unit,detail_devis.ref,detail_devis.srv_unique_id FROM `detail_devis` JOIN detail_broker_devis ON detail_devis.srv_unique_id = detail_broker_devis.srv_unique_id WHERE `id_devis`='$id' GROUP BY `empl` LIMIT $offset,12;";
                }else{
                    $query = "SELECT * FROM `detail_devis` WHERE `id_devis`='$id' GROUP BY `empl` LIMIT $offset,12 ";
                }
                $res = mysqli_query($cnx, $query);
                $row = mysqli_fetch_all($res);
            
            
        $total=0;


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
                            $total+=sprintf('%05.2f', round(floatval($prix)*floatval($data[4]),2));
                            
                        }
                    
                
                

                // <!-- for loop bracket -->
                
                    //foreach loop the offset take plus 10 for the limit that means retieving data will start from the $offset row
                    $offset += 12;
                    
                    if($i+1 != $splitRows){
                        $html .= '</tbody></table>';
                        $html .= '<div class="page-break"></div>';
                        $html .= '<div style="margin-top:5rem;"></div>';
                        
                    }
                    }
                // <!-- TOTALS -->
                if(isset($_GET['broker_id'])){
                    $total=sprintf('%05.2f', $total);
                    $html .= '<tr class="text-bold">
                        <td colspan="5">TOTAL H.T</td>
                        <td>'.$total.'</td>
                    </tr>';
                }else{
                    $html .= '<tr class="text-bold">
                        <td colspan="5">TOTAL H.T</td>
                        <td>'.$devisInfo["sub_total"].'</td>
                    </tr>';
                    
                    if($devisInfo['remove_tva']=="0"){
                        $html .= '
                        <tr class="text-bold">
                        <td colspan="5">TVA 20%</td>
                        <td>'. sprintf('%05.2f', round(floatval($devisInfo['sub_total'])*0.2,2)).'</td>
                        </tr>
                        <tr class="text-bold">
                        <td colspan="5">TOTAL T.T.C</td>
                        <td>'.$devisInfo['net_total'].'</td>
                        </tr>';
                    }
                }
                    
                
            $html .= '</tbody>
        </table>';
                    echo $html;
                ?>
        

        <!-- <div class="page-break"></div> -->
        <div class="my-3">
            <span>Arrêté la présent Devis à la somme de:</span>
            <!-- <?php var_dump($devisInfo)?> -->
            <p class="underline"style="text-align:center;padding:0 20px;font-size:0.9rem;"><strong><?php 
             if(isset($_GET['broker_id'])){
                 echo intergerIntoFrenchWords($total); 
             }else{
                echo intergerIntoFrenchWords($devisInfo['net_total']); 
             }
            ?> 
            <span><?php 
            if(isset($_GET['broker_id'])){
                echo 'H.T.';
            }else{
                if(($devisInfo['remove_tva']=="0")) {echo('T.T.C.');}else{ echo 'H.T.';}
            }
            ?></span> </strong></p>
        </div>
        <div class="my-5"style="font-size:0.7rem;line-height:0.5;z-index:1">
            <span class='underline'><strong>Condition de vente:</strong></span>
            <p>Ce Devis est valable pour une durée de <strong class="underline">30 jour</strong>.</p>
            <p>Veuillez nous retourner le Devis signé et précédé de la montion : <strong class="underline">Bon pour accorder et commande ,Date</strong> et <strong class="underline">Sigature.</strong></p>
            <p>Ce Devis sera considéré comme <strong class="underline">Bon de commande</strong> une fois qu'il nous sera retourné signé et cacheté par vous soins.</p>
            <p>Toute modification équivant à une <strong class="underline">annulation</strong> de ce Devis.</p>
            <p><strong class="underline">Condition de règlement :</strong></p>
            <p>Acompte de 90% au début des travaux.</p>
            <p>Acompte de 10% à la livraison des travaux</p>
            <p style="float:right;margin-right:10px;font-size:0.9rem">Signé le Directeur général</p><br>
            <img src="<?php echo $base64_1 ?>" width="240" style="margin-left:330px; margin-top:50px;"/>
        <!-- </div><br><br><br><br> -->
      
        <!-- <div style="z-index:0;position:fixed;left:0;bottom:1;">
            <img src="" alt="Watermark">
        </div> -->
        <!-- <section style="border:1px solid green;z-index:1">
            <div style="text-align:right;font-size:0.7rem;position:fixed;bottom:0;right:0;line-height:0;border:1px solid red;">
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