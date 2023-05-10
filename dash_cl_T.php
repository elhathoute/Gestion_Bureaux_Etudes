<?php
    include 'includes/config.php';
    include 'functions.php';

    $period = $_POST['period'];
    $rows = countClientDash($period);
    echo $rows;
?>