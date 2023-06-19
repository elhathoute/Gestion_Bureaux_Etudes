<?php

include 'includes/config.php';
include 'functions.php';
$selectedMonth = $_POST['selectedMonth'];
$selectedYear = $_POST['selectedYear'];
// $query = "SELECT SUM(montant_paye) FROM devis_payments WHERE (pay_method='Check' OR pay_method='Virement' OR pay_method='Traite' OR pay_method='Remis') AND MONTH(pay_date) = $selectedMonth";
if(isset($_POST['selectedYear'])){
    $query = "SELECT pay_date from devis_payments WHERE YEAR(pay_date)=$selectedYear GROUP by YEAR(pay_date);";
}
if(isset($_POST['selectedMonth'])){
    $query = "SELECT pay_date from devis_payments WHERE MONTH(pay_date)=$selectedMonth GROUP by YEAR(pay_date);";
}
$res = mysqli_query($cnx, $query);
// $row = mysqli_fetch_assoc($res);
$data = array();
$number = 1;
while($row=mysqli_fetch_assoc($res)){
    $year = date("Y", strtotime($row['pay_date']));
    $month = date("n", strtotime($row['pay_date']));
    $month2 = date("m", strtotime($row['pay_date']));
    $subarray = array();
    $subarray[] = $number;
    $subarray[] = 'Caise '.$month2.'/'.$year;
    $subarray[] = '<a target="_blank" href="caise_export.php?year='.$year.'&month='.$month.'" class="btn btn-primary btn-sm" title="Afficher Caise" ><span><i class="bi bi-download"></i></span></a>';
    $data[] = $subarray;
    $number++;
}
$output = array('data'=>$data);
echo json_encode($output);

// var_dump($row);