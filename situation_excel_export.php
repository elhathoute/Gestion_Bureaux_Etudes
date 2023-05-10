<?php

include 'includes/config.php';

function filterData(&$str){
    $str = preg_replace("/\t/","\\t",$str);
    $str = preg_replace("/\r?\n/","\\n",$str);
    if(strstr($str,'"')) $str = '"' . str_replace('"','""',$str) . '"';
}

// Excel file name for download

$filename = "situation_" . date('d-m-Y_h-i-s') . ".xls";

// Column Names

$fields = array('N°','Date','Ref','Nom de service','Maitre D\'ouvrage','Prix','Regle','Paye','Reste');

// Display column names as first row 

$excelData = implode("\t",array_values($fields)) . "\n";

// Fetch records from Database


if($_GET){
    $clientId = $_GET["cl_id"];
    $query = "CALL `sp_getDevisSituation`('".$clientId."');";

    // Check if the URI Hold the specific vars to excute the Right Procedure else will excute the default _SP_
    if(isset($_GET["pd_st"]) && isset($_GET["srv_name"])){

        $paid_status = $_GET["pd_st"];
        $srv_name = str_replace("%20"," ", $_GET["srv_name"]);
        $query = "CALL `sp_getDevisSituationBoth`('".$clientId."','".$paid_status."','".$srv_name."');";

    }elseif (isset($_GET["pd_st"])) {

        $paid_status = $_GET["pd_st"];
        $query = "CALL `sp_getDevisSituationStatus`('".$clientId."','".$paid_status."');";

    }elseif (isset($_GET["srv_name"])){

        $srv_name = str_replace("%20"," ", $_GET["srv_name"]);
        $query = "CALL `sp_getDevisSituationSrv`('".$clientId."','".$srv_name."');";
    }








    $res = mysqli_query($cnx,$query);
    
    $solde = 0;
    $total = 0;
    $num = 1;
    if(mysqli_num_rows($res) > 0){

        while($row = mysqli_fetch_assoc($res)){
            $price = $row["remove_tva"] == '0'? sprintf('%05.2f',round($row["prix"] * 1.2,2)) : sprintf('%05.2f',round($row["prix"],2));
            $status = $row["paid_srv"] == '1' ? "Payé" : "Non payé";
            $date = new DateTime($row["date_creation"]);
            $formated_date = $date->format('d/m/Y');
            $avance = sprintf('%05.2f',round($row["avance"],2));
            $remain = sprintf('%05.2f',round($price - $row["avance"],2));
    
            $lineData = array($num, $formated_date, $row["ref"], $row['service_name'], $row['client'], $price, $status, $avance, $remain);
            array_walk($lineData,'filterData');
            $excelData .= implode("\t", array_values($lineData)) . "\n";

            $num++;
        }
    }
    else
    {
        $excelData .= "No records found..." . "\n";
    }

    // Headers for download
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename\"");

    // Render Excel Data

    echo $excelData;

    exit;

}