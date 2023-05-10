<?php
include "includes/config.php";
include "functions.php";

$res=getServiceData();
$refs = array();
while($row=mysqli_fetch_assoc($res)){
    $refs[$row["title"]] = $row["ref"];
}

$detailDevisRes = getAllDetailDevis();
$service_ref = array();
while($row=mysqli_fetch_assoc($detailDevisRes)){
    $service_ref[$row["service_name"]] = $row["ref"];
}

$detailInvoiceRes = getAllDetailInvoice();
$invoiceService_ref = array();
while($row=mysqli_fetch_assoc($detailInvoiceRes)){
    $invoiceService_ref[$row["service_name"]] = $row["ref"];
}

//merge the arrays and get rid of the duplicated services
$output = array_merge($refs,$service_ref,$invoiceService_ref);
echo json_encode($output);

?>