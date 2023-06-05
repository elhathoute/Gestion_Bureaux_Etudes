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
.underline{
    text-decoration: underline;
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
.S_table{
    border-collapse: collapse;
    margin-right: 22px;
    float:right;
}
.S_table, .S_table tr,.S_table td{
border: none;
}
    </style>
</head>
<body>
<?php

// if(isset($_GET['id'])){
//     $pay_id = $_GET['id'];
//     $receiptInfo = getReceipt($pay_id);
// }
if(isset($_GET['R_number'])){
    $R_number = $_GET['R_number'];
    $receiptInfo = getReceipt($R_number);
}
// var_dump($receiptInfo[0]);
// die();

$html = '';
?>
<?php
$path = 'images/BeplanLogo.png';
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
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
                    <p class="reçu_num" style="text-align: center;"><strong><u>N°<?php echo $receiptInfo[0][1]?></u></strong></p>
                </div>
            </div>
            <div style="background-color:aliceblue">
                <pre>
                Paye Par                : <strong><?= $receiptInfo[0][8]?></strong><br>
                <?php if($receiptInfo[0][12]=='broker'){ ?>
Intermédiaire         : <strong><?= $receiptInfo[0][13]?></strong><br>
                <?php }else{?>
M.O                       : <strong><?= $receiptInfo[0][4]?></strong><br>
                <?php }?>
Date de paiement   : <strong><?=date("d/m/Y",strtotime($receiptInfo[0][5])) ;?></strong><br>
                Mode de paiement  : <strong><?= $receiptInfo[0][2]?></strong><br>
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
            <th>Service</th>
            <th>Prix</th>
            <th>Montant Paye</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
            <?php 
            $number=1;
            $Total_paye =0;
            $Total_prix=0;
                foreach($receiptInfo as $row){
                    if($row[6]==$row[7]){
                        $status ='Payé';
                    }elseif($row[7]<$row[6]){
                        if($row[6]==$row[11]){
                        $status ='Payé';
                        }else{
                        $status ='Avance';
                        }
                    }
                    if($row[10]!=NULL){
                        $Dossier_N =$row[10];
                    }else{
                        $Dossier_N ='-';
                    }
                    
                $html .= '<tr>';
                $html .= '<td>'.$number.'</td>';
                $html .= '<td> '.strtoupper($row[0]).' </td>';
                $html .= '<td>'.$Dossier_N .'</td>';
                $html .= '<td> '.strtoupper($row[3]).' </td>';
                $html .= '<td> '.$row[6].' </td>';
                $html .= '<td>'.$row[7].'</td>';
                $html .= '<td> '.$status.' </td>';
                $html .= '</tr>';
                $Total_paye+=$row[7];
                $Total_prix+=$row[6];
                if($row[11]==$row[6]){
                    $avance=$row[11]-$row[7];
                    $Total_prix-=$avance;
                }
                $number++;
            }
            $html .= '</table>';
            $html .= '<br>';
            $html .= '<table class="S_table">';
            $html .= '<tbody>';
            $html .= '<tr>';
            $html .= '<td colspan="4"> <strong>TOTAL PRIX :</strong> </td>';
            $html .= '<td colspan="3"><strong> '.$Total_prix.' DH</strong> </td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="4"> <strong>TOTAL PAYE :</strong> </td>';
            $html .= '<td colspan="3"><strong> '.$Total_paye.' DH</strong> </td>';
            $html .= '</tr>';
            $html .= '</tbody>';
            $html .= '</table>';
            echo  $html;
                ?>
        </tbody>
    </table>
    <!-- <h6>Audits et rapports mensuels (1er Novembre 2016 - 30 Novembre 2016)</h6> -->

    <!--  end of the table  -->
    <br>
    <div style="clear:right">
        <p style="float:right;margin-right:10px;font-size:1.3rem" class="underline">Signé par order de directeur général</p><br>
    </div>


    <
  </div>
</div>

</body>

</html>