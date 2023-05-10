<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu d'achat</title>
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
        td {
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
    </style>
</head>

<body>
    <?php

        if(isset($_GET['p'])){
            $p_id = $_GET['p'];
            $purchaseInfo = getPurchaseReceit($p_id);
            
        
    
    
    ?>
    <div class="container">
        
        <!-- <section >
            <div class="img-container">
                <img src="<?php //echo $base64; ?> " alt="Logo Company">
            </div>
        </section> -->
        

        <section style="margin-top:4rem">
            <div class="my-5">
                <div style="margin:auto;width:fit-content;text-align:center;font-weight:600;font-size:1.3rem;line-height:1.5">
                    <!-- <span>A</span><br> -->
                    <span class="underline">Reçu d'Achat</span><br>
                    <span style="text-decoration:underline">Numéro <?=$purchaseInfo['P_number'];?></span>
                </div>
            </div>
        </section>

        <br><br><br><br>
        
        <section>
            <div class="my-5"style="font-size:1.2rem;line-height:2;">
                <span>Désignation: <strong><?=ucfirst($purchaseInfo["name"]);?></strong></span>
                <!-- <p>M.O : <strong><?=strtoupper($receiptInfo["client"]);?></strong></p> -->
                <p>La somme total de : <strong class="underline"><?=$purchaseInfo["price"];?> DHS</strong></p>
                <p>Pour : <span class="underline"><?=ucfirst($purchaseInfo["note"]);?>.<span></p>
                <!-- <p>Mode de paiement : <?=ucfirst($receiptInfo['pay_method']);?></p> -->
                <p>Date d'achat : <?=date("d/m/Y",strtotime($purchaseInfo["date"])) ;?>.</p>
            </div><br><br><br><br>
            <div>
                <p style="float:right;margin-right:10px;font-size:1.3rem" class="underline">Signé par order de directeur général</p><br>
            </div>
        </section>
        
    </div>
    
    <?php } ?>

</body>

</html>