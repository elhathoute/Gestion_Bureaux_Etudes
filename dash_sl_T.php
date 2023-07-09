<?php
    include 'includes/config.php';
    include 'functions.php';

    $period = $_POST['period'];

    // sales 
    $sales = countInvDashSales($period);
    // clients
    $clients = countClientDash($period);
    //revenue
    $revenue = countInvPayDash($period);
    $nonpayservices=countPayServices($period);
    $dossier=countDossier($period);
    $devis=countDevis($period);
    $devisPrix=countDevisPrix($period);
    $brokers=countBrokertDash($period);
    $output = array("sales"=>$sales,"clients"=>$clients,"revenue"=>$revenue,"nonpayservices"=>$nonpayservices,"dossier"=>$dossier,"devis"=>$devis,"devisPrix"=>$devisPrix,"brokers"=>$brokers);
    echo json_encode($output);
?>