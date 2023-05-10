<?php
    include 'includes/config.php';
    include 'functions.php';

    $period = $_POST['period'];
    $rows = countInvPayDash($period);
    echo $rows;
?>