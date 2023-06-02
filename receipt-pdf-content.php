<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu</title>
    <!-- <link rel="stylesheet" href="css/bootstrap.min.css"> -->
    <style>
* {
    box-sizing: border-box;
    font-family: 'Times New Roman', Times, serif;
    }
/* .container {
    width: 15cm; 
   min-height: 100%;
}*/

.invoice {
    background: #fff;
    width: 100%;
    margin-bottom: 0;
}

.logo {
    width: 2.5cm;
}

.document-type {
    /* text-align: left; */
    color: #444;
}

.conditions {
    font-size: 0.7em;
    color: #666;
}

.bottom-page {
    font-size: 0.7em;
    /* margin-bottom: auto; */
}
.beplan_logo{
    text-align: center;
}
.be{
    color: #235D93;
    /* font-family: ; */
}
.reçu_header{
    display: flex;
}
table,th,tr,td {
            border: 1px solid black;
            text-align: center;
            padding: 5px;
}
.table {
            border-collapse: collapse;
            width: 100%;
            font-size: 1rem;
}
    </style>
</head>
<body>
<?php

if(isset($_GET['id'])){
    $pay_id = $_GET['id'];
    $receiptInfo = getReceipt($pay_id);
}
?>
<?php
$path = 'images/BeplanLogo.png';
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
// $path2 = 'images/BeplanLogo_2.png';
// $type2 = pathinfo($path2, PATHINFO_EXTENSION);
// $data2 = file_get_contents($path2);
// $base642 = 'data:image/' . $type2 . ';base64,' . base64_encode($data2);
?>
    <div class="container">
        <div class="invoice">
            <div class="reçu_header">
                <div>
                    <div class="beplan_logo">
                    <img src="<?php echo $base64?>" width="240"/>
                </div>
                <div class="">
                    <h2 class="document-type" style="text-align: center;">Reçu de Paiment</h2>
                    <p class="reçu_num" style="text-align: center;"><strong><u>N°<?=$receiptInfo['R_number'];?></u></strong></p>
                </div>
            </div>
            <div style="background-color:aliceblue">
                <pre>
                Paye Par     : <strong><?=ucfirst($receiptInfo["pay_giver"]);?></strong><br>
                M.O            : <strong><?=strtoupper($receiptInfo["client"]);?></strong><br>
                Description : <strong><?=$receiptInfo["objet"];?></strong>
                </pre>
            </div>
        </div>
    <!-- start of the table  -->
    <br>
    <table class="table">
        <thead>
            <tr>
            <th>N°</th>
            <th>Devis N°</th>
            <th>Dossier N°</th>
            <th>Ref</th>
            <th>Service</th>
            <th>Mode de paiement</th>
            <th>Date de paiement</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>12/23</td>
                <td>NSW001</td>
                <td>CF</td>
                <td><?=strtoupper($receiptInfo["service_name"]);?></td>
                <td><?=ucfirst($receiptInfo['pay_method']);?></td>
                <td><?=date("d/m/Y",strtotime($receiptInfo["pay_date"])) ;?></td>
            </tr>
            <tr>
                <td colspan="5"><strong>Service Prix</strong></td>
                <td colspan="2"><strong><?=$receiptInfo["prix"];?> DHS</strong></td>
            </tr>
            <tr>
                <td colspan="5">Montant Paye</td>
                <td colspan="2"><?=$receiptInfo["montant_paye"];?></td>
            </tr>
            <tr>
                <td colspan="5">Total Montant Paye</td>
                <td colspan="2"><?=$receiptInfo["montant_paye"];?></td>
            </tr>
            <tr>
                <td colspan="5"><strong>Rest</strong></td>
                <td colspan="2"><strong><?=$receiptInfo["prix"];?> DHS</strong></td>
            </tr>
        </tbody>
    </table>
    <h6>Audits et rapports mensuels (1er Novembre 2016 - 30 Novembre 2016)</h6>

    <!--  end of the table  -->
    <div>
        <p style="float:right;margin-right:10px;font-size:1.3rem" class="underline">Signé par order de directeur général</p><br>
    </div>


    <
  </div>
</div>

</body>

</html>