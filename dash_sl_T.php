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
    $output = array("sales"=>$sales,"clients"=>$clients,"revenue"=>$revenue);
    echo json_encode($output);
?>