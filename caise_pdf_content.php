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
    </style>
</head>
<body>
<?php
// var_dump($_GET);
$selectedYear=$_GET['year'];
$selectedMonth=$_GET['month'];

$caisedatails= getCaiseDetails($selectedMonth,$selectedYear);
$query = "SELECT SUM(amount_given) , supplier.full_name  FROM supplier_details JOIN supplier ON supplier.id =supplier_details.supplier_id WHERE YEAR(paye_date) = $selectedYear AND MONTH(paye_date) = $selectedMonth GROUP by supplier_details.supplier_id;";
$res= mysqli_query($cnx,$query);  
$suppliersDetails=  mysqli_fetch_all($res);
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
                    <h2 class="document-type" style="text-align: center;">Suivi du Caise Date du <?=date('01-m-Y', strtotime(date("01-$selectedMonth-$selectedYear")))?></h2>
                    <!-- <p class="reçu_num" style="text-align: center;"><strong><u>N°</u></strong></p> -->
                </div>
            </div>
        </div>
    <!-- start of the table  -->
    <br>
    <table class="table">
        <thead>
        <tr>
            <th> Libellé </th>
            <th> Crédit</th>
            <th>Débit</th>
            <th> Solde</th>
        </tr>
        </thead>
        <tbody>
            <?php 
            $number=1;
            $débitTotal=0;
            $OrderPaiment = $caisedatails['montantEspice'] + $caisedatails['purchasePrice'];
            $html .= '<tr>';
            $html .= '<td> Situation des Reçu de Paiment</td>';
            $html .= '<td>'.$caisedatails['totalPaiment'] .'</td>';
            $html .= '<td> </td>';
            $html .= '<td> '.$caisedatails['totalPaiment'] .'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td> Orders de Paiment</td>';
            $html .= '<td></td>';
            $html .= '<td>'.$OrderPaiment.'</td>';
            $html .= '<td> '.($caisedatails['totalPaiment'] -$OrderPaiment ).'</td>';
            $html .= '</tr>';
            $débitTotal+=$OrderPaiment;
            $sold=($caisedatails['totalPaiment'] -$OrderPaiment );
            if($suppliersDetails!=''){
                foreach($suppliersDetails as $index => $row){
                    if($index == 0){
                        $sold= $sold- $row[0];
                    }else{
                        $sold=$sold - $row[0];
                    }
                    $html .= '<tr>';
                    $html .= '<td>'.$row[1].'</td>';
                    $html .= '<td></td>';
                    $html .= '<td>'.$row[0].'</td>';
                    $html .= '<td>'.$sold.'</td>';
                    $html .= '</tr>';
                    $débitTotal+=$row[0];
                }
                $html .= '<tr>';
                $html .= '<td>Beplan</td>';
                $html .= '<td></td>';
                $html .= '<td>'.$sold.'</td>';
                $html .= '<td></td>';
                $html .= '</tr>';
                $débitTotal+=$sold;
            }else{
                $html .= '<tr>';
                $html .= '<td>Beplan</td>';
                $html .= '<td></td>';
                $html .= '<td>'.$sold.'</td>';
                $html .= '<td></td>';
                $html .= '</tr>';
                $débitTotal+=$sold;
            }

            $html .= '<tr>';
            $html .= '<th>Total</th>';
            $html .= '<th>'.$caisedatails['totalPaiment'] .'</th>';
            $html .= '<th>'.$débitTotal.'</th>';
            $html .= '<th></th>';
            $html .= '</tr>';

            $html .= '</table>';
            $html .= '<br>';
            $html .= '<table class="table">';
            $html .= '<tbody>';
            $html .= '<tr>';
            $html .= '<td > <strong>SOLD AU '.date('t-m-Y', strtotime(date("01-$selectedMonth-$selectedYear"))).'</strong> </td>';
            $html .= '<td><strong>'.($caisedatails['totalPaiment'] -$débitTotal ).'</strong> </td>';
            $html .= '</tbody>';
            $html .= '</table>';
            echo  $html;
                ?>
        </tbody>
    </table>

    <
  </div>
</div>

</body>

</html>