<?php

//fetch individual client data

function getIndvClientData(){
    // connect to database
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT * FROM `client_individual` WHERE `delete_status`='0'";
    $res = mysqli_query($cnx,$query);
    return $res;
}

//fetch entreprise client data

function getEntrepClientData(){
    // connect to database
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT * FROM `client_entreprise` WHERE `delete_status`='0'";
    $res = mysqli_query($cnx,$query);
    return $res;
}

//fetch service data

function getServiceData(){
    // connect to database
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT * FROM `service` ";
    $res = mysqli_query($cnx,$query);
    return $res;
}



// load select services function 

function fill_service_dropDown(){
    // connect to database
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT * FROM `service`";
    $res = mysqli_query($cnx,$query);
    $output = '';
    while($row = mysqli_fetch_assoc($res)){
        $title = $row["title"];
        $output .= '<option id='.$row['id'].' class="servicesTitleOption" value ="'.$title.'" ></option>';
    }
    return $output;
}


function getDevisNumber(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT number FROM `devis` WHERE `remove`='0';";
    $res = mysqli_query($cnx,$query);
    if(mysqli_num_rows($res)==0){
        return "1";
    }
    $nums_arr = mysqli_fetch_all($res);
    
    $numbers = array();
    //array of the complete invoice number as a strings 
    $all_inv_number = array();
    foreach($nums_arr as $val){
        $numbers[] = intval(explode('/',$val[0])[0]);
        //adding to array
        $all_inv_number[] = $val[0];
    }
    
    $compare_array = range(1,max($numbers));
    //array of numbers concatinated with the '/' and the current year
    $compare_numbers_array = array();
    foreach($compare_array as $number){
        //adding to array
        $compare_numbers_array[] = sprintf("%03d",$number) .'/'.date('Y');
    }

    $missing_values = array_diff($compare_numbers_array,$all_inv_number);
    //array of the missing numbers (the database format 00X/XXXX)
    $missing_numbers = array();
    
    foreach($missing_values as $val){
        //adding to array
        $missing_numbers[] = intval(explode('/',$val)[0]);
    }


    if(count($missing_numbers)!=0){
        // if array not empty 
        return min($missing_numbers);
    }else{
        // if array empty 
        return max($numbers)+1;
    }
    
}
//function to style devis status 
function styleStatus($status){
    if(strtolower($status) == 'encours'){
        return "badge text-bg-warning";
    }elseif (strtolower($status) == 'accepter') {
        return "badge text-bg-success";
    }elseif (strtolower($status) == 'rejeter') {
        return "badge text-bg-danger";
    }
}   

//function to fetch selected devis services
function getSelectedDevisServices(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    if($_GET){
        $id = $_GET['id'];
        $query = "SELECT * FROM `detail_devis` WHERE `id_devis`='$id'";
        $res = mysqli_query($cnx,$query);
        $row = mysqli_fetch_all($res);
        // print_r($row);
        $html = '';
        foreach ($row as $val) {
            $html .= '<tr>';
            $html .= '<td><i class="bi bi-trash fs-5 deleteRowBtn" ></i></td>';
            $html .= '<td class="input-group"><input type="text" class="input-group-text w-25 servRefTxt" id="srvRT" value="'.$val[7].'" placeholder="Reference" autocomplete="off" required data-bs-placement="bottom" data-bs-content="Cette référence existe déjà" data-bs-trigger="manual" data-bs-custom-class="error-popover"><input type="text" id="servicesListId" list="servicesList"  autocomplete="off" value="'.$val[2].'" class="form-control serviceDropdown" aria-describedby="srvRT"><datalist id="servicesList"> '.fill_service_dropDown().'</datalist></td>';
            $html .= '<td><input type="text" name="" class="form-control py-1 serviceUnit" value="'.$val[6].'"  placeholder="Unité"></td>';
            $html .= '<td><input type="number" min="0" name="" class="form-control py-1 px-1 rowServiceQte"  value="'.$val[4].'" placeholder="Quantité"></td>';
            $html .= '<td><input type="number" min="0"  step="0.01" name="" class="form-control py-1 px-1 servicePrice"  value="'.$val[3].'" placeholder="0.00"></td>';
            $html .= '<td><div class="input-group"><span class="input-group-text py-1"><i class="bi bi-percent"></i></span><input type="number"  min="0" name="" value="'.$val[5].'" class="form-control py-1 serviceDiscount" placeholder="Enter % (ex: 10%)"></div></td>';
            $html .= '<td><input type="text" name="" class="form-control py-1 rowServiceTotal" disabled placeholder="0"></td>';
            $html .= '</tr>';
        }
        return $html;
    }
}

function viewDevisServices(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    if($_GET){
        $id = $_GET['id'];
        $query = "SELECT * FROM `detail_devis` WHERE `id_devis`='$id'";
        $res = mysqli_query($cnx,$query);
        $row = mysqli_fetch_all($res);
        // print_r($row);
        $devis_type = getDevisById($id)['type'];
        if(strtolower($devis_type)=="approved"){

        }
        $html = '';
        foreach ($row as $val) {
            $check_client = '';
            if(strtolower($devis_type)=="approved"){
                $check_client = '<span><i class="bi bi-check-circle btn btn-outline-success btn-sm rounded-circle btn-client-approve" data-id="'.$val[0].'" title="Devis Approuvé par Client" ></i></span> ';
                if($val[9]){
                    $check_client = '<span><i class="bi bi-x-circle btn btn-outline-danger btn-sm rounded-circle btn-cancel-client-approve" data-id="'.$val[0].'" title="Annuler l\'approbation" ></i></span>';
                }
            }
            if($val[8] == "1"){
                $check_client ='';
            }
            // $success_icon = (strtolower($devis_type)=="approved")? '<i class="bi bi-check-circle btn btn-outline-success btn-sm rounded-circle btn-client-approve" data-id="'.$val[0].'" title="Devis Approuvé par Client" ></i>': '';
            $client_approve = ($val[9]=="1")? "approved_row" :"";
            $html .= '<tr class="'.$client_approve.'" >';
            $html .= '<td>'.$check_client.'</td>';
            $html .= '<td class="input-group"><input type="text" class="input-group-text w-25 servRefTxt" id="srvRT" value="'.$val[7].'" placeholder="Reference" autocomplete="off" required data-bs-placement="bottom" data-bs-content="Cette référence existe déjà" data-bs-trigger="manual" data-bs-custom-class="error-popover"><input type="text" id="servicesListId" list="servicesList"  autocomplete="off" value="'.$val[2].'" class="form-control serviceDropdown" aria-describedby="srvRT"><datalist id="servicesList"> '.fill_service_dropDown().'</datalist></td>';
            $html .= '<td><input type="text" name="" class="form-control py-1 serviceUnit" value="'.$val[6].'"  placeholder="Unité"></td>';
            $html .= '<td><input type="number" min="0" name="" class="form-control py-1 px-1 rowServiceQte"  value="'.$val[4].'" placeholder="Quantité"></td>';
            $html .= '<td><input type="number" min="0"  step="0.01" name="" class="form-control py-1 px-1 servicePrice"  value="'.$val[3].'" placeholder="0.00"></td>';
            $html .= '<td><div class="input-group"><span class="input-group-text py-1"><i class="bi bi-percent"></i></span><input type="number"  min="0" name="" value="'.$val[5].'" class="form-control py-1 serviceDiscount" placeholder="Enter % (ex: 10%)"></div></td>';
            $html .= '<td><input type="text" name="" class="form-control py-1 rowServiceTotal" disabled placeholder="0"></td>';
            $html .= '</tr>';
        }
        return $html;
    }
}


// broker devis

function viewBrokerDevisServices(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    if($_GET){
        $id = $_GET['id'];
        $query = "SELECT * FROM `detail_devis` WHERE `id_devis`='$id'";
        $res = mysqli_query($cnx,$query);
        $row = mysqli_fetch_all($res);
        // print_r($row);
        $devis_type = getDevisById($id)['type'];
        if(strtolower($devis_type)=="approved"){

        }
        $html = '';
        foreach ($row as $val) {
            $check_client = '';
            if(strtolower($devis_type)=="approved"){
                $check_client = '<span><i class="bi bi-check-circle btn btn-outline-success btn-sm rounded-circle btn-client-approve" data-id="'.$val[0].'" title="Devis Approuvé par Client" ></i></span> ';
                if($val[9]){
                    $check_client = '<span><i class="bi bi-x-circle btn btn-outline-danger btn-sm rounded-circle btn-cancel-client-approve" data-id="'.$val[0].'" title="Annuler l\'approbation" ></i></span>';
                }
            }
            if($val[8] == "1"){
                $check_client ='';
            }
            // $success_icon = (strtolower($devis_type)=="approved")? '<i class="bi bi-check-circle btn btn-outline-success btn-sm rounded-circle btn-client-approve" data-id="'.$val[0].'" title="Devis Approuvé par Client" ></i>': '';
            $client_approve = ($val[9]=="1")? "approved_row" :"";
            $html .= '<tr class="'.$client_approve.'" >';
            // $html .= '<td>'.$check_client.'</td>';
            $html .= '<td></td>';
            $html .= '<td class="input-group"><input type="text" class="input-group-text w-25 servRefTxt" id="srvRT" value="'.$val[7].'" placeholder="Reference" autocomplete="off" required data-bs-placement="bottom" data-bs-content="Cette référence existe déjà" data-bs-trigger="manual" data-bs-custom-class="error-popover"><input type="text" id="servicesListId" list="servicesList"  autocomplete="off" value="'.$val[2].'" class="form-control serviceDropdown" aria-describedby="srvRT"><datalist id="servicesList"> '.fill_service_dropDown().'</datalist></td>';
            $html .= '<td><input type="text" name="" class="form-control py-1 serviceUnit" value="'.$val[6].'"  placeholder="Unité"></td>';
            $html .= '<td><input type="number" min="0" name="" class="form-control py-1 px-1 rowBrkServiceQte"  value="'.$val[4].'" placeholder="Quantité"></td>';
            $html .= '<td><input type="number" min="0"  step="0.01" name="" class="form-control py-1 px-1 serviceBrkPrice"  value="'.$val[3].'" placeholder="0.00"></td>';
            $html .= '<td><div class="input-group"><span class="input-group-text py-1"><i class="bi bi-percent"></i></span><input type="number"  min="0" name="" value="'.$val[5].'" class="form-control py-1 serviceBrkDiscount" placeholder="Enter % (ex: 10%)"></div></td>';
            $html .= '<td><input type="text" name="" class="form-control py-1 rowServiceBrkTotal" disabled placeholder="0"></td>';
            $html .= '</tr>';
        }
        return $html;
    }
}

//function to fetch selected devis information

function getSelectedDevisInfo(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    if($_GET){
        $id = $_GET['id'];
        $query = "SELECT * FROM `devis` WHERE `id`='$id' AND `remove`=0";
        $res = mysqli_query($cnx,$query);
        $row = mysqli_fetch_assoc($res);
        return $row; 

    }
}

// Fetch detail_devis data
function getAllDetailDevis(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "SELECT DISTINCT `service_name`,`ref` FROM `detail_devis`;";
    $res = mysqli_query($cnx,$query);
    return $res;
}

//get detail_devis by id
function getDetailDevisById($detailId){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT * FROM `detail_devis` WHERE `id`='$detailId'";
    $res = mysqli_query($cnx,$query);
    $row = mysqli_fetch_assoc($res);
    return $row;

}



//fetch selected Client 
function getSelectedClientName(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    if($_GET){
        // $id = $_GET['id'];
        $client_id =$_GET['client_id'];
        $query = "SELECT * FROM `client` WHERE `id`='$client_id'";
        $res = mysqli_query($cnx,$query);
        $row = mysqli_fetch_assoc($res);
        $clientRow='';
        if($row['type']=='individual'){
            $query = 'SELECT * FROM `client_individual` WHERE `id`='.$row['id_client'].'';
            $res = mysqli_query($cnx,$query);
            $clientRow = mysqli_fetch_assoc($res);
            return $clientRow["prenom"] .' '. $clientRow['nom'];
        }
        else{
            $query = 'SELECT * FROM `client_entreprise` WHERE `id`='.$row['id_client'].'';
            $res = mysqli_query($cnx,$query);
            $clientRow = mysqli_fetch_assoc($res);
            return $clientRow['nom'];
        }
        // return $clientRow;
    }

}

function fetchClientName($type,$id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    if($type=='individual'){
        $query = 'SELECT * FROM `client_individual` WHERE `id`='.$id.'';
        $res = mysqli_query($cnx,$query);
        $clientRow = mysqli_fetch_assoc($res);
        return $clientRow["prenom"] .' '. $clientRow['nom'];
    }
    else{
        $query = 'SELECT * FROM `client_entreprise` WHERE `id`='.$id.'';
        $res = mysqli_query($cnx,$query);
        $clientRow = mysqli_fetch_assoc($res);
        return $clientRow['nom'];
    }
}

function getSelectedClientAdr(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    if($_GET){
        $client_id =$_GET['client_id'];
        $query = "SELECT * FROM `client` WHERE `id`='$client_id'";
        $res = mysqli_query($cnx,$query);
        $row = mysqli_fetch_assoc($res);
        // $clientRow='';
        if($row['type']=='individual'){
            $query = 'SELECT * FROM `client_individual` WHERE `id`='.$row['id_client'].'';
            $res = mysqli_query($cnx,$query);
            $clientRow = mysqli_fetch_assoc($res);
            // return $clientRow["prenom"] .' '. $clientRow['nom'];
            return $clientRow['address'];
        }
        else{
            $query = 'SELECT * FROM `client_entreprise` WHERE `id`='.$row['id_client'].'';
            $res = mysqli_query($cnx,$query);
            $clientRow = mysqli_fetch_assoc($res);
            // return $clientRow['nom'];
            return $clientRow['address'] . '/' . $clientRow['ICE'];
        }
    }
}

function getClientId($id,$type){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    
    // $client_id =$_GET['client_id'];
    $query = "SELECT * FROM `client` WHERE `id_client`='$id' AND `type`='$type'";
    $res = mysqli_query($cnx,$query);
    $row = mysqli_fetch_assoc($res);
    return $row['id'];
}

//function numbers to words in french


function intergerIntoFrenchWords($int)
{
    $number = explode(".",$int);
    $after_comma = ($number[1]=="0" || $number[1]=="00")?"": " et ".$number[1] ." cents";

    $int = intval($number[0]);
    if(intval($int) == 0) return 'zéro';
    elseif (intval($int)> 999999999) return false;
    $units = array(
        1=>'un',
        2=>'deux',
        3=>'trois',
        4=>'quatre',
        5=>'cinq',
        6=>'six',
        7=>'sept',
        8=>'huit',
        9=>'neuf'
    );
    $exceptions = array(
        11=>"onze",
        12=>'douze',
        13=>'treize',
        14=>'quatorze',
        15=>'quinze',
        16=>'seize'
    );
    $thousands = array(
        1=>"mille",
        2=>'million',
        3=>'milliard'
    );
    $teens = array(
        1=> "dix",
        2 => "vingt",
        3=> "trente",
        4 => "quarante",
        5 => "cinquante",
        6 => "soixante",
        7 => "soixante",
        8 => "quatre-vingt",
        9 => "quatre-vingt",
    );

    $return ='';

    $number = number_format($int);
    $splitNumber = explode(',', $number);
    $nbThousandPacket = count($splitNumber);

    for($i=0; $i<$nbThousandPacket; $i++)
    {
        $value = $splitNumber[$i];
        $unit = intval(substr($value, -1));
        $dix = intval(substr($value, -2));
        $dixaine = intval(floor($dix/10));
        $cent = intval(floor($value/100));
        $forException = ($dixaine == 7 ||  $dixaine == 9) ? $dix - (($dixaine -1)*10) : null;

        if($cent>0){
            $return .=  ($cent > 1) ? $units[$cent].' cents ':' cent ';
        }
        if($dix > 9){
            if(array_key_exists($dix, $exceptions)) $return .= $exceptions[$dix];
            else {
                $return .= $teens[$dixaine];
                if ($dixaine == 7 ||  $dixaine == 9) {
                    if($forException && array_key_exists($forException, $exceptions)) {
                        if($forException == 11 && $dixaine == 7) $return .= ' et ';
                        else $return .= '-';
                        $return .= $exceptions[$forException];
                    }
                    else $return .= '-dix';
                }
            }
        }
        if($unit == 1 && strlen($return) > 0 && $dixaine < 7 && $dixaine > 1) $return .= ' et '.$units[$unit];
        elseif ($unit > 0 && $dixaine == 8 ) $return .= '-'.$units[$unit];
        //elseif ($unit > 0 && ($dixaine == 7 || $dixaine == 9) && !(($forException && array_key_exists($forException, $exceptions)) ||  array_key_exists($dix, $exceptions))) $return .= '-'.$units[$unit];
        elseif ($unit > 0 && $dixaine != 0 && !(($forException && array_key_exists($forException, $exceptions)) ||  array_key_exists($dix, $exceptions))) $return .= '-'.$units[$unit];
        elseif ($unit > 0 && $dixaine == 0) $return .= ' '.$units[$unit];

        $thousandKey = $nbThousandPacket - ($i+1);
        if(array_key_exists($thousandKey, $thousands))  {
            $return .= ' '.$thousands[$thousandKey];
            if($thousandKey > 1 && $value >1)$return .= 's';
            $return .= ' ';
        }
    }
    return trim(ucwords($return)." Dirhams " . $after_comma);
}
// Fetch all roles

function getAllRoles(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $role_id = getUserRoleId($user_id);
    
    $query = "SELECT * FROM `roles` WHERE `id`<> '3' AND `id`<> '$role_id'";
    $res = mysqli_query($cnx,$query);
    return $res;
}
//fetch Permissions based on role id

function getPerm($roleId){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "SELECT t2.id,t2.perm_desc FROM `role_perm` as t1
              JOIN `permissions` as t2 ON t1.perm_id = t2.id
              WHERE t1.role_id = $roleId";

    $res = mysqli_query($cnx,$query);
    return $res;
}

//fetch users role name to dropdown

function getUserRoleId($user_id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query ="SELECT * FROM `user_role` WHERE `user_id` = $user_id";
    $res = mysqli_query($cnx,$query);
    $row = mysqli_fetch_assoc($res);
    //fetch role id
    $role_id = $row["role_id"];
    return $role_id;

}
//fetch user row

function getUser($user_id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT * FROM `users` WHERE `id`= '$user_id'";
    $res = mysqli_query($cnx, $query);
    $row = mysqli_fetch_assoc($res);
    return $row;
}
// get role of user
function getUserRole($user_id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT roles.role_name,users.id FROM `users`
    INNER JOIN user_role on users.id=user_role.user_id
    INNER JOIN roles on roles.id=user_role.role_id WHERE users.id='$user_id';";
    $res = mysqli_query($cnx, $query);
    $row = mysqli_fetch_assoc($res);
    return $row;
}
//get notifications number
function getNotifCount(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
   
    // $query = "SELECT * FROM `notifications` WHERE `active`='1'";
    $query = "CALL `sp_getNotifications`();";
    $res = mysqli_query($cnx, $query);
   
    $num = mysqli_num_rows($res);
   
    return $num + notifInvNumRows() + notifPaymentNumRows();
}

//get invoice notification procedure num rows
function notifInvNumRows(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "CALL `sp_getInvoiceNotifications`();";
    $res = mysqli_query($cnx, $query);
    $num = mysqli_num_rows($res);
    return $num;
}
//get Payment notification procedure num rows
function notifPaymentNumRows(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "CALL `sp_getPaymentNotification`();";
    $res = mysqli_query($cnx, $query);
    $num = mysqli_num_rows($res);
    return $num;
}


//invoice getNumber

function getInvoiceNumber(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT F_number FROM `invoice` WHERE `remove`='0';";
    $res = mysqli_query($cnx,$query);
    if(mysqli_num_rows($res)==0){
        return "1";
    }
    $nums_arr = mysqli_fetch_all($res);
    
    $numbers = array();
    //array of the complete invoice number as a strings 
    $all_inv_number = array();
    foreach($nums_arr as $val){
        $numbers[] = intval(explode('/',$val[0])[0]);
        //adding to array
        $all_inv_number[] = $val[0];
    }
    
    $compare_array = range(1,max($numbers));
    //array of numbers concatinated with the '/' and the current year
    $compare_numbers_array = array();
    foreach($compare_array as $number){
        //adding to array
        $compare_numbers_array[] = sprintf("%03d",$number) .'/'.date('Y');
    }

    $missing_values = array_diff($compare_numbers_array,$all_inv_number);
    //array of the missing numbers (the database format 00X/XXXX)
    $missing_numbers = array();
    
    foreach($missing_values as $val){
        //adding to array
        $missing_numbers[] = intval(explode('/',$val)[0]);
    }
    
    if(count($missing_numbers)!=0){
        // if array not empty 
        return min($missing_numbers);
    }else{
        // if array empty 
        return max($numbers)+1;
    }
}

//fetch selected invoice info

function getSelectedInvoiceInfo(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    if($_GET){
        $id = $_GET['id'];
        $query = "SELECT * FROM `invoice` WHERE `id`='$id' AND `remove`=0";
        $res = mysqli_query($cnx,$query);
        $row = mysqli_fetch_assoc($res);
        return $row; 

    }
}

//function to fetch selected invoice services
function getSelectedInvoiceServices(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    if($_GET){
        $id = $_GET['id'];
        $query = "SELECT * FROM `detail_invoice` WHERE `id_invoice`='$id'";
        $res = mysqli_query($cnx,$query);
        $row = mysqli_fetch_all($res);
        // print_r($row);
        $html = '';
        foreach ($row as $val) {
            $html .= '<tr>';
            $html .= '<td><i class="bi bi-trash fs-5 deleteRowBtn" ></i></td>';
            $html .= '<td class="input-group"><input type="text" class="input-group-text w-25 servRefTxt" id="srvRT" value="'.$val[7].'" placeholder="Reference" autocomplete="off" required data-bs-placement="bottom" data-bs-content="Cette référence existe déjà" data-bs-trigger="manual" data-bs-custom-class="error-popover"><input type="text" id="servicesListId" list="servicesList"  autocomplete="off" value="'.$val[2].'" class="form-control serviceDropdown" aria-describedby="srvRT"><datalist id="servicesList"> '.fill_service_dropDown().'</datalist></td>';
            $html .= '<td><input type="text" name="" class="form-control py-1 serviceUnit" value="'.$val[6].'"  placeholder="Unité"></td>';
            $html .= '<td><input type="number" min="0" name="" class="form-control py-1 px-1 rowServiceQte"  value="'.$val[4].'" placeholder="Quantité"></td>';
            $html .= '<td><input type="number" min="0"  step="0.01" name="" class="form-control py-1 px-1 servicePrice"  value="'.$val[3].'" placeholder="0.00"></td>';
            $html .= '<td><div class="input-group"><span class="input-group-text py-1"><i class="bi bi-percent"></i></span><input type="number"  min="0" name="" value="'.$val[5].'" class="form-control py-1 serviceDiscount" placeholder="Enter % (ex: 10%)"></div></td>';
            $html .= '<td><input type="text" name="" class="form-control py-1 rowServiceTotal" disabled placeholder="0"></td>';
            $html .= '</tr>';
        }
        return $html;
    }
}
function devisNotificationData(){
    $cnx = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "CALL `sp_getNotifications`();";
    $res = mysqli_query($cnx,$query);
    $html= '';
    while($row = mysqli_fetch_assoc($res)){
        $user = getUser($row['id_user']);
        $html .= '<tr>';
        $html .= '<td>'.ucfirst($user['prenom'])." ".ucfirst($user['nom']) .'</td>';
        $html .= '<td>'.$row['number'].'</td>';
        $html .= '<td>Devis</td>';
        $html .= '<td>'.$row['date'].'</td>';
        $html .= '<td>'.$row['action'].'</td>';
        $html .= '
        <form action="notification-action.php" id="notificationForm" method="POST">

        <td><a target="_blank" href="devis_export.php?id='.$row['id_devis'].'&client_id='.$row['id_client'].'" class="btn btn-secondary btn-sm"  ><span><i class="bi bi-eye"></i></span></a>
                    <a href="devis-edit.php?id='.$row['id_devis'].'&client_id='.$row['id_client'].'" data-id="'.$row['id_devis'].'" class="btn btn-primary btn-sm editDevisBtn"><span><i class="bi bi-pencil-square"></i></span></a>
                    &nbsp;
                   
                    <input title="Accepter" type="submit" name="btn-approve-notif" class="btn btn-success btn-sm btn-approve-notif" value="Approve"/>
                    <input title="Annuler" type="submit" name="btn-decline-notif" class="btn btn-danger btn-sm btn-decline-notif" value="Decline"/>
                    <input type="hidden" name="devis_id" value="'.$row['id_devis'].'">
                    <input type="hidden" name="doc_type" value="devis">
                    </form>
                </td>';
        $html .= '</tr>';
    }
    return $html;
}
function invoiceNotificationData(){
    $cnx = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "CALL `sp_getInvoiceNotifications`();";
    $res = mysqli_query($cnx, $query);
    $html = '';
    while ($row = mysqli_fetch_assoc($res)) {
        $user = getUser($row['id_user']);
        $html .= '<tr>';
        $html .= '<td>' . ucfirst($user['prenom']) . " " . ucfirst($user['nom']) . '</td>';
        $html .= '<td>' . $row['F_number'] . '</td>';
        $html .= '<td>Facture</td>';
        $html .= '<td>' . $row['date'] . '</td>';
        $html .= '<td>'.$row['action'].'</td>';

        $html .= '
        <form action="notification-action.php" id="notificationForm" method="POST">

        <td><a target="_blank" href="invoice_export.php?id=' . $row['id_invoice'] . '&client_id=' . $row['id_client'] . '" class="btn btn-secondary btn-sm"><span><i class="bi bi-eye"></i></span></a>
                    <a href="invoice-edit.php?id=' . $row['id_invoice'] . '&client_id=' . $row['id_client'] . '" data-id="' . $row['id_invoice'] . '" class="btn btn-primary btn-sm editInvoiceBtn"><span><i class="bi bi-pencil-square"></i></span></a>
                    &nbsp;
                    <input type="submit" name="btn-approve-notif" class="btn btn-success btn-sm btn-approve-notif" value="Approve"/>
                    <input type="submit" name="btn-decline-notif" class="btn btn-danger btn-sm btn-decline-notif" value="Decline"/>
                    <input type="hidden" name="invoice_id" value="' . $row['id_invoice'] . '">
                    <input type="hidden" name="doc_type" value="invoice">
                </form>
                    </td>';
        $html .= '</tr>';
    }
    return $html;
}

//fetching the first row from invoice for payment by client
function getFirstPayInvoice($client_id){
    $cnx = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "SELECT * FROM `invoice` WHERE id_client = $client_id AND `type`='Approved' AND `remove`=0 AND `paid_inv`=0 ORDER BY `date_creation` ASC LIMIT 1;";
    $res = mysqli_query($cnx, $query);
    if($res){
        return $res;
    }
}

//fetching the first row from devis for payment by client
function getFirstPayDevis($client_id){
    $cnx = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "SELECT devis.* FROM `devis` INNER JOIN `detail_devis` ON devis.id = detail_devis.id_devis WHERE id_client = $client_id  AND `remove`=0 AND detail_devis.paid_srv = 0 ORDER BY `date_creation` ASC LIMIT 1";
    $res = mysqli_query($cnx, $query);
    if($res){
        return $res;
    }
}

//Get all devis_detail ids
function getDevisDetails($devis_res){
    $cnx = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $detailIds = array();

    $devis_id = $devis_res['id'];
    
    $query = "SELECT * FROM `detail_devis` WHERE `id_devis` = '$devis_id'";
    $res = mysqli_query($cnx,$query);
    while($row = mysqli_fetch_assoc($res)){
        $detailIds[] = $row['id'];
    }

    return $detailIds;
}


//insert data to invoice_payments
function payInvoice($invoice_id,$price,$pay_method){
    $cnx = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $user_id = $_SESSION['user_id'];
    $query = "INSERT INTO `invoice_payments`(`id`, `id_invoice`, `prix`, `pay_method`,`user_id`) VALUES (null,'$invoice_id','$price','$pay_method','$user_id')";
    mysqli_query($cnx,$query);
    $last_id = mysqli_insert_id($cnx);
    return $last_id;
    
}

//insert data to devis_payments
function payDevis($devis_id,$price,$pay_method){
    $cnx = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $user_id = $_SESSION['user_id'];
    $query = "INSERT INTO `devis_payments`(`id`, `id_devis`, `prix`, `pay_method`,`user_id`) VALUES (null,'$devis_id','$price','$pay_method','$user_id')";
    mysqli_query($cnx,$query);
    $last_id = mysqli_insert_id($cnx);
    return $last_id;
    
}


//get sum invoice prices

function getSumInvoicePrices($invoice_id){
    $cnx = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT SUM(`prix`) AS price FROM `invoice_payments` WHERE `id_invoice` = $invoice_id;";
    $res = mysqli_query($cnx,$query);
    $row = mysqli_fetch_assoc($res);
    if($res){
        return floatval($row["price"]);
    }
}

//get sum devis prices

function getSumDevisPrices($devis_id){
    $cnx = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT SUM(`prix`) AS price FROM `devis_payments` WHERE `id_devis` = $devis_id;";
    $res = mysqli_query($cnx,$query);
    $row = mysqli_fetch_assoc($res);
    if($res){
        return floatval($row["price"]);
    }
}

function updateInvoicePaidStatus($invoice_id){
    $cnx = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "UPDATE `invoice` SET `paid_inv`='1' WHERE `id`='$invoice_id'";
    mysqli_query($cnx,$query);

}

//updating service paid status
function updateServicePaidStatus($detailDevis_id){
    $cnx = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "UPDATE `detail_devis` SET `paid_srv`='1',`srv_avance` = '0' WHERE `id`='$detailDevis_id'";
    mysqli_query($cnx,$query);

}

//update detail avance

function updateDetailAvance($detailDevis_id,$amount){
    $cnx = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "UPDATE `detail_devis` SET `srv_avance` = '$amount' WHERE `id`='$detailDevis_id'";
    mysqli_query($cnx,$query);
}



//calling procedure for fetching payments info
//this function is for invoice payment
/**
 * This function is for invoice payment info 
*/
// function getPaymentsInfo(){
//     $cnx = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
//     if (mysqli_connect_errno()) {
//         echo "Failed to connect to MySQL: " . mysqli_connect_error();
//         exit();
//     }

//     $query = "CALL `sp_getPaymentInfo`();";
//     $res = mysqli_query($cnx,$query);
//     $html = '';
//     while($row=mysqli_fetch_assoc($res)){
//         $userRow = getUser($_SESSION['user_id']);
//         $user = ucfirst($userRow["prenom"]) . ' ' . ucfirst($userRow['nom']);
//         $html .= '<tr>';
//         $html .= '<td> '.ucfirst($row["pay_method"]).' </td>';
//         $html .= '<td> '.ucfirst($row["F_number"]).' </td>';
//         $html .= '<td> '.$user.' </td>';
//         $html .= '<td> '.$row["client"].' </td>';
//         $html .= '<td> '.$row["pay_date"].' </td>';
//         $html .= '<td> '.$row["prix"].' </td>';
//         $html .= '<td class="text-center"> <a target="_blank" href="receipt_export.php?id='.$row["pay_id"].'" title="Imprimer Reçu"><i class="bi bi-paperclip"></i></a> </td>';
//         $html .= '</tr>';
//     }
//     return $html;
// }

//for devis payment info
function getPaymentsInfo(){
    $cnx = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "CALL `sp_getDevisPaymentInfo`();";
    $res = mysqli_query($cnx,$query);
    $html = '';
    while($row=mysqli_fetch_assoc($res)){
        $userRow = getUser($_SESSION['user_id']);
        $user = ucfirst($userRow["prenom"]) . ' ' . ucfirst($userRow['nom']);
        $html .= '<tr>';
        $html .= '<td> '.ucfirst($row["pay_method"]).' </td>';
        $html .= '<td> '.ucfirst($row["number"]).' </td>';
        $html .= '<td> '.$user.' </td>';
        $html .= '<td> '.$row["client"].' </td>';
        $html .= '<td> '.$row["pay_date"].' </td>';
        $html .= '<td> '.$row["prix"].' </td>';
        $html .= '<td class="text-center"> <a target="_blank" href="receipt_export.php?id='.$row["pay_id"].'" title="Imprimer Reçu"><i class="bi bi-paperclip"></i></a> </td>';
        $html .= '</tr>';
    }
    return $html;
}



//accept payment function
function acceptPayment($devis_id){
    $cnx = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "UPDATE `devis_payments` SET `pending`='0' WHERE `id_devis`='$devis_id' AND `pending`='1'";
    mysqli_query($cnx,$query);
}

//decline payment function
function declinePayment($detail_id){
    $cnx = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "DELETE FROM `devis_payments` WHERE `id_devis`='$detail_id' AND `pending`='1'";
    mysqli_query($cnx,$query);
}


//update srv_notif 
function setServiceNotif($detailId){
    $cnx = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $q = "SELECT * FROM `detail_devis` WHERE `id`='$detailId'";
    $res = mysqli_query($cnx,$q);
    $row = mysqli_fetch_assoc($res);
    if($row['srv_notif'] != 0){
        $query = "UPDATE `detail_devis` SET `srv_notif`='0' WHERE `id` = '$detailId'";
    }else{
        $query = "UPDATE `detail_devis` SET `srv_notif`='1' WHERE `id` = '$detailId'";
    }
    mysqli_query($cnx,$query);
}

//payment notification 
function paymentNotificationData(){
    $cnx = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "CALL `sp_getPaymentNotification`();";
    $res = mysqli_query($cnx, $query);
    $html = '';
    while ($row = mysqli_fetch_assoc($res)) {
        $user = getUser($row['user_id']);
        $html .= '<tr>';
        $html .= '<td>' . ucfirst($user['prenom']) . " " . ucfirst($user['nom']) . '</td>';
        $html .= '<td>' . $row['number'] . '</td>';
        $html .= '<td>Paiement</td>';
        $html .= '<td>' . $row['pay_date'] . '</td>';
        $html .= '<td>Paiement</td>';

        $html .= '<td>
        <form action="notification-action.php" id="notificationForm" method="POST">

        <a target="_blank" href="receipt_export.php?id='.$row["pay_id"].'" class="btn btn-secondary btn-sm"  ><span><i class="bi bi-eye"></i></span></a>
                    &nbsp;
                    <input type="submit" name="btn-approve-notif" class="btn btn-success btn-sm btn-approve-notif" value="Approve"/>
                    <input type="submit" name="btn-decline-notif" class="btn btn-danger btn-sm btn-decline-notif" value="Decline"/>
                    <input type="hidden" name="id_devis" value="' . $row['id_devis'] . '">
                    <input type="hidden" name="id_detail" value="' . $row['detail_id'] . '">
                    <input type="hidden" name="doc_type" value="payment">
              </form>
                    </td>';
        $html .= '</tr>';
    }
    return $html;
}

//reciept number function

function getReceiptNumber(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT `R_number` FROM `receipt`;";
    $res = mysqli_query($cnx,$query);
    if(mysqli_num_rows($res)==0){
        return "1";
    }
    $nums_arr = mysqli_fetch_all($res);
    
    $numbers = array();
    //array of the complete invoice number as a strings 
    $all_inv_number = array();
    foreach($nums_arr as $val){
        $numbers[] = intval(explode('-',$val[0])[0]);
        //adding to array
        $all_inv_number[] = $val[0];
    }
    
    $compare_array = range(1,max($numbers));
    //array of numbers concatinated with the '/' and the current year
    $compare_numbers_array = array();
    foreach($compare_array as $number){
        //adding to array
        $compare_numbers_array[] = sprintf("%03d",$number).'-'. date('m') .'/'.date('Y');
    }
    $missing_values = array_diff($compare_numbers_array,$all_inv_number);
    //array of the missing numbers (the database format 00X/XXXX)
    $missing_numbers = array();
    
    foreach($missing_values as $val){
        //adding to array
        $missing_numbers[] = intval(explode('/',$val)[0]);
    }

    if(count($missing_numbers)!=0){
        // if array not empty 
        return min($missing_numbers);
    }else{
        // if array empty 
        return max($numbers)+1;
    }
}

//add receipt to payment

function addReceipt($paymentId,$pay_giver){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $receiptNumber = sprintf("%03d", getReceiptNumber()).'-'. date('m') .'/'.date('Y');

    $query = "INSERT INTO `receipt`(`id`, `R_number`, `id_payment`,`pay_giver`) VALUES (null,'$receiptNumber','$paymentId','$pay_giver');";
    mysqli_query($cnx,$query);
}


function getReceipt($payment_id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    //this stored procedure for invoice receipt
    // $query = "CALL `sp_getReceipt`('$payment_id');";

    //this stored procedure for devis receipt
    $query = "CALL `sp_getDevisReceipt`('$payment_id');";
    $res = mysqli_query($cnx,$query);
    $row = mysqli_fetch_assoc($res);
    return $row;

}

//Change the Payment_made value in detail_devis
function setPay_made($detailId){
    $cnx = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "UPDATE `detail_devis` SET `payment_made`='1' WHERE `id`='$detailId'";
    mysqli_query($cnx,$query);
}



//generate purchase number

function getPurchaseNumber(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT P_number FROM `purchase` WHERE `remove`='0';";
    $res = mysqli_query($cnx,$query);
    if(mysqli_num_rows($res)==0){
        return "1";
    }
    $nums_arr = mysqli_fetch_all($res);
    
    $numbers = array();
    //array of the complete invoice number as a strings 
    $all_inv_number = array();
    foreach($nums_arr as $val){
        $numbers[] = intval(explode('/',$val[0])[0]);
        //adding to array
        $all_inv_number[] = $val[0];
    }
    
    $compare_array = range(1,max($numbers));
    //array of numbers concatinated with the '/' and the current year
    $compare_numbers_array = array();
    foreach($compare_array as $number){
        //adding to array
        $compare_numbers_array[] = sprintf("%03d",$number) .'/'.date('Y');
    }
    
    $missing_values = array_diff($compare_numbers_array,$all_inv_number);
    //array of the missing numbers (the database format 00X/XXXX)
    $missing_numbers = array();
    
    foreach($missing_values as $val){
        //adding to array
        $missing_numbers[] = intval(explode('/',$val)[0]);
    }
    
    if(count($missing_numbers)!=0){
        // if array not empty 
        return min($missing_numbers);
    }else{
        // if array empty 
        return max($numbers)+1;
    }
}

//get All purchases

function getPurchaseInfo(){

    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT * FROM `purchase` WHERE `remove`='0';";
    $res = mysqli_query($cnx,$query);
    $html = '';
    while($row=mysqli_fetch_assoc($res)){
        $html .= '<tr>';
        $html .= '<td>'.$row['name'].'</td>';
        $html .= '<td>'.$row['note'].'</td>';
        $html .= '<td>'.number_format($row['price'],2).' DH</td>';
        $html .= '<td>'.$row['date'].'</td>';
        $html .= '<td class="text-center"> <a target="_blank" href="purchase_export.php?p='.$row["id"].'" title="Imprimer Reçu"><i class="bi bi-paperclip"></i></a> </td>';
        $html .= '<td>
                    <a href="purchase-edit.php?p_id='.$row["id"].'" data-id="" class="btn btn-primary btn-sm editPurchaseBtn" title="Éditer Achat"><span><i class="bi bi-pencil-square"></i></span></a>
                    <a href="javascript:void(0)" data-id="'.$row["id"].'" class="btn btn-danger btn-sm deletePurchaseBtn" title="Effacer Achat"><span><i class="bi bi-trash"></i></span></a>
                </td>';
        $html .= '</tr>';
    }
    return $html;
}

//fetch purchase by id
function getPurchaseReceit($purchase_id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "SELECT * FROM `purchase` WHERE `id`='$purchase_id' AND `remove`='0';";
    $res = mysqli_query($cnx,$query);
    $row = mysqli_fetch_assoc($res);
    return $row;

}

// insert into user_client history

function userClient_history($user,$client,$type,$action){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "INSERT INTO `user_client`(`id_user`, `id_client`, `cl_type`, `action`) VALUES ('$user','$client','$type','$action');";
    mysqli_query($cnx,$query);
}
// fetching userClient_history Data
function userClient_historyData(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "SELECT * FROM `user_client`;";
    $res = mysqli_query($cnx,$query);
    $html = '';
    while($row = mysqli_fetch_assoc($res)){
        $userRow = getUser($row['id_user']);
        $clientName = ucfirst(fetchClientName($row['cl_type'],$row['id_client']));
        $html .= '<tr>';
        $html .= '<td>'.ucfirst($userRow['prenom'])." ".ucfirst($userRow['nom']) .'</td>';
        $html .= '<td>'.$clientName.'</td>';
        $html .= '<td>'.$row['action'].'</td>';
        $html .= '<td>'.$row['date'].'</td>';
        $html .= '</tr>';
    }
    return $html;
}

// insert into user_service history

function userService_history($user,$service,$action){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "INSERT INTO `user_service`(`id_user`, `service`, `action`) VALUES ('$user','$service','$action');";
    mysqli_query($cnx,$query);
}

// fetching userService_history Data
function userService_historyData(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "SELECT * FROM `user_service`;";
    $res = mysqli_query($cnx,$query);
    $html = '';
    while($row = mysqli_fetch_assoc($res)){
        $userRow = getUser($row['id_user']);
        // $query = "SELECT * FROM `service` WHERE `id`='".$row['id_service']."';";
        // $res = mysqli_query($cnx,$query);
        // $serviceName = getServiceById($row['id_service'])['title'];
        $serviceName = $row['service'];
        $html .= '<tr>';
        $html .= '<td>'.ucfirst($userRow['prenom'])." ".ucfirst($userRow['nom']) .'</td>';
        $html .= '<td>'.$serviceName.'</td>';
        $html .= '<td>'.$row['action'].'</td>';
        $html .= '<td>'.$row['date'].'</td>';
        $html .= '</tr>';
    }
    return $html;
}

function getServiceById($id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "SELECT * FROM `service` WHERE `id`='$id';";
    $res = mysqli_query($cnx,$query);
    return mysqli_fetch_assoc($res);
}

// insert into user_Devis history

function userDevis_history($user,$devis,$action){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "INSERT INTO `user_devis`(`id_user`, `id_devis`, `action`) VALUES ('$user','$devis','$action')";
    mysqli_query($cnx,$query);
}

//get devis by id
function getDevisById($id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "SELECT * FROM `devis` WHERE `id`='$id';";
    $res = mysqli_query($cnx,$query);
    $row = mysqli_fetch_assoc($res);
    return $row;
}

// fetching userDevis_history Data
function userDevis_historyData(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "SELECT * FROM `user_devis`;";
    $res = mysqli_query($cnx,$query);
    $html = '';
    while($row = mysqli_fetch_assoc($res)){
        $userRow = getUser($row['id_user']);
        
        $devisNumber = getDevisById($row['id_devis'])['number'];
        $html .= '<tr>';
        $html .= '<td>'.ucfirst($userRow['prenom'])." ".ucfirst($userRow['nom']) .'</td>';
        $html .= '<td>'.$devisNumber.'</td>';
        $html .= '<td>'.$row['action'].'</td>';
        $html .= '<td>'.$row['date'].'</td>';
        $html .= '</tr>';
    }
    return $html;
}

// insert into user_Invoice history

function userInvoice_history($user,$invoice,$action){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "INSERT INTO `user_invoice`(`id_user`, `id_invoice`, `action`) VALUES ('$user','$invoice','$action')";
    mysqli_query($cnx,$query);
}

//get invoice by id
function getInvoiceById($id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "SELECT * FROM `invoice` WHERE `id`='$id';";
    $res = mysqli_query($cnx,$query);
    $row = mysqli_fetch_assoc($res);
    return $row;
}

// fetching userInvoice_history Data
function userInvoice_historyData(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "SELECT * FROM `user_invoice`;";
    $res = mysqli_query($cnx,$query);
    $html = '';
    while($row = mysqli_fetch_assoc($res)){
        $userRow = getUser($row['id_user']);
        
        $invoiceNumber = getInvoiceById($row['id_invoice'])['F_number'];
        $html .= '<tr>';
        $html .= '<td>'.ucfirst($userRow['prenom'])." ".ucfirst($userRow['nom']) .'</td>';
        $html .= '<td>'.$invoiceNumber.'</td>';
        $html .= '<td>'.$row['action'].'</td>';
        $html .= '<td>'.$row['date'].'</td>';
        $html .= '</tr>';
    }
    return $html;
}

// insert into user_Purchase history
function userPurchase_history($user,$purchase,$action){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "INSERT INTO `user_purchase`(`id_user`, `id_purchase`, `action`) VALUES ('$user','$purchase','$action')";
    mysqli_query($cnx,$query);
}

//get purchase by id
function getPurchaseById($id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "SELECT * FROM `purchase` WHERE `id`='$id';";
    $res = mysqli_query($cnx,$query);
    $row = mysqli_fetch_assoc($res);
    return $row;
}

// fetching userPurchase_history Data
function userPurchase_historyData(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "SELECT * FROM `user_purchase`;";
    $res = mysqli_query($cnx,$query);
    $html = '';
    while($row = mysqli_fetch_assoc($res)){
        $userRow = getUser($row['id_user']);
        
        $purchaseNumber = getPurchaseById($row['id_purchase'])['P_number'];
        $html .= '<tr>';
        $html .= '<td>'.ucfirst($userRow['prenom'])." ".ucfirst($userRow['nom']) .'</td>';
        $html .= '<td>'.$purchaseNumber.'</td>';
        $html .= '<td>'.$row['action'].'</td>';
        $html .= '<td>'.$row['date'].'</td>';
        $html .= '</tr>';
    }
    return $html;
}
function last_login($usr_id,$date){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "UPDATE `users` SET `last_login`='$date' WHERE `id`='$usr_id';";
    mysqli_query($cnx,$query);
}

function getInvDetailById($invoice_id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT * FROM `detail_invoice` WHERE `id_invoice`='$invoice_id'";
    $res = mysqli_query($cnx,$query);
    $row = mysqli_fetch_all($res);
    return $row;
}

// Fetch detail_invoice data
function getAllDetailInvoice(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "SELECT DISTINCT `service_name`,`ref` FROM `detail_invoice`;";
    $res = mysqli_query($cnx,$query);
    return $res;
}




//add Situation 
function addSituation($client_id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "INSERT INTO `situation`(`id_client`) VALUES ('$client_id')";
    mysqli_query($cnx,$query);
    $id = mysqli_insert_id($cnx);
    return $id;
}
//count customers based on the period
function countClientDash($period){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    // $query = "";
    // if(strtolower($period) == 'week'){
    //     $curDate = date('Y-m-d');
    //     $date=date_create(date("Y-m-d"));
    //     date_add($date,date_interval_create_from_date_string("1 week ago"));
    //     $weekAgo = date_format($date,"Y-m-d");
    //     $query = "SELECT * FROM `client` WHERE DATE(date) BETWEEN '$weekAgo' AND '$curDate';";
    // }elseif(strtolower($period) == 'month'){
    //     $curMonth = date('m');
    //     $query = "SELECT * FROM `client` WHERE MONTH(date) = '$curMonth' ;";
    // }elseif(strtolower($period) == 'year'){
    //     $curYear = date('Y');
    //     $query = "SELECT * FROM `client` WHERE YEAR(date) = '$curYear' ;";
    // }

    $from = $period[0];
    $to = $period[1];

    $query = "SELECT * FROM `client` WHERE DATE(date) BETWEEN '$from' AND '$to';";
    $res = mysqli_query($cnx,$query);
    $rows = mysqli_num_rows($res);
    return $rows;
}
//count invoices For dashbord sales
function countInvDashSales($period){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    // $query = "";
    // if(strtolower($period) == 'week'){
    //     $curDate = date('Y-m-d');
    //     $date=date_create(date("Y-m-d"));
    //     date_add($date,date_interval_create_from_date_string("1 week ago"));
    //     $weekAgo = date_format($date,"Y-m-d");
    //     $query = "SELECT * FROM `invoice` WHERE DATE(date_creation) BETWEEN '$weekAgo' AND '$curDate';";
    // }elseif(strtolower($period) == 'month'){
    //     $curMonth = date('m');
    //     $query = "SELECT * FROM `invoice` WHERE MONTH(date_creation) = '$curMonth' ;";
    // }elseif(strtolower($period) == 'year'){
    //     $curYear = date('Y');
    //     $query = "SELECT * FROM `invoice` WHERE YEAR(date_creation) = '$curYear' ;";
    // }



    // $period_arr = explode("-",$period);
    // $trimedTo = strtotime(trim($period_arr[1]));
    // $to = $period_arr[1];
    $from = $period[0];
    $to = $period[1];
    
    $query = "SELECT * FROM `invoice` WHERE DATE(date_creation) BETWEEN '$from' AND '$to' AND `type`='Approved';";




    $res = mysqli_query($cnx,$query);
    $rows = mysqli_num_rows($res);
    return $rows;
}
//count invoices payemnts For dashbord revenue
function countInvPayDash($period){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    // $query = "";
    // if(strtolower($period) == 'week'){
    //     $curDate = date('Y-m-d');
    //     $date=date_create(date("Y-m-d"));
    //     date_add($date,date_interval_create_from_date_string("1 week ago"));
    //     $weekAgo = date_format($date,"Y-m-d");
    //     $query = "SELECT IFNULL(SUM(net_total),0) AS price FROM `invoice` WHERE DATE(date_creation) BETWEEN '$weekAgo' AND '$curDate';";
    // }elseif(strtolower($period) == 'month'){
    //     $curMonth = date('m');
    //     $query = "SELECT IFNULL(SUM(net_total),0) AS price FROM `invoice` WHERE MONTH(date_creation) = '$curMonth' ;";
    // }elseif(strtolower($period) == 'year'){
    //     $curYear = date('Y');
    //     $query = "SELECT IFNULL(SUM(net_total),0) AS price FROM `invoice` WHERE YEAR(date_creation) = '$curYear' ;";
    // }

    $from = $period[0];
    $to = $period[1];

    $query = "SELECT IFNULL(SUM(net_total),0) AS price FROM `invoice` WHERE DATE(date_creation) BETWEEN '$from' AND '$to' AND `type`='Approved';";


    $res = mysqli_query($cnx,$query);
    $priceSum = mysqli_fetch_assoc($res);
    return $priceSum['price'];
}

//function to count how many invoice got created on the current week
function weeklyDashSales(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $curDate = date('Y-m-d');
    $date=date_create(date("Y-m-d"));
    date_add($date,date_interval_create_from_date_string("1 week ago"));
    $weekAgo = date_format($date,"Y-m-d");

    $query = "SELECT DATE(date_creation) AS Day,COUNT(*) AS somme FROM invoice WHERE DATE(date_creation) BETWEEN '$weekAgo' AND '$curDate' GROUP BY DATE(date_creation);";
    $res = mysqli_query($cnx, $query);
    $days = array();
    $invs = array();
    while($row=mysqli_fetch_assoc($res)){
        $days[] = $row["Day"];
        $invs[] = $row["somme"];
    }
    $sales = ["days"=>$days,"invs"=>$invs];
    return $sales;
}

//function to count how many client got created on the current week
function weeklyDashCustomers(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $curDate = date('Y-m-d');
    $date=date_create(date("Y-m-d"));
    date_add($date,date_interval_create_from_date_string("1 week ago"));
    $weekAgo = date_format($date,"Y-m-d");

    $query = "SELECT DATE(date) AS Day,COUNT(*) AS somme FROM client WHERE DATE(date) BETWEEN '$weekAgo' AND '$curDate' GROUP BY DATE(date);";
    $res = mysqli_query($cnx, $query);
    $days = array();
    $invs = array();
    while($row=mysqli_fetch_assoc($res)){
        $days[] = $row["Day"];
        $invs[] = $row["somme"];
    }
    $sales = ["days"=>$days,"invs"=>$invs];
    return $sales;
}
//function to count calculate the total foreach day in the last week
function weeklyDashRevenue(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $curDate = date('Y-m-d');
    $date=date_create(date("Y-m-d"));
    date_add($date,date_interval_create_from_date_string("1 week ago"));
    $weekAgo = date_format($date,"Y-m-d");

    $query = "SELECT DATE(date_creation) AS Day,SUM(net_total) AS somme FROM invoice  WHERE DATE(date_creation) BETWEEN '$weekAgo' AND '$curDate' GROUP BY DATE(date_creation);";
    $res = mysqli_query($cnx, $query);
    $days = array();
    $invs = array();
    while($row=mysqli_fetch_assoc($res)){
        $days[] = $row["Day"];
        $invs[] = $row["somme"];
    }
    $sales = ["days"=>$days,"invs"=>$invs];
    return $sales;
}

//function to count rows in detail_devis
function countDevisRows($id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT * FROM `detail_devis` WHERE `id_devis`='$id'";
    $res = mysqli_query($cnx, $query);
    $rows = mysqli_num_rows($res);
    return $rows;
}

//function to count rows in detail_invoice
function countInvoiceRows($id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT * FROM `detail_invoice` WHERE `id_invoice`='$id'";
    $res = mysqli_query($cnx, $query);
    $rows = mysqli_num_rows($res);
    return $rows;
}
//function to check url variables

function checkUrlVars($table,$id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT id FROM `$table`;";
    $res = mysqli_query($cnx, $query);
    $rows = mysqli_fetch_all($res);
    // $i = array();
    $exist = false;
    foreach($rows as $rowId){
        if($rowId[0] == $id){
            $exist = true;
            return;
        }
    }
    if(!$exist){
        header("location:page-error-404.php");
        die();
    }

}

//broker fetch all Data
function getBrokerData(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT * FROM `broker` ";
    $res = mysqli_query($cnx,$query);
    return $res;
}

// insert into user_broker history

function userBroker_history($user,$broker,$action){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "INSERT INTO `user_broker`(`id_user`, `broker`, `action`) VALUES ('$user','$broker','$action');";
    mysqli_query($cnx,$query);
}

// fetching userBroker_history Data
function userBroker_historyData(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "SELECT * FROM `user_broker`;";
    $res = mysqli_query($cnx,$query);
    $html = '';
    while($row = mysqli_fetch_assoc($res)){
        $userRow = getUser($row['id_user']);
        $brokerName = $row['broker'];
        $html .= '<tr>';
        $html .= '<td>'.ucfirst($userRow['prenom'])." ".ucfirst($userRow['nom']) .'</td>';
        $html .= '<td>'.$brokerName.'</td>';
        $html .= '<td>'.$row['action'].'</td>';
        $html .= '<td>'.$row['date'].'</td>';
        $html .= '</tr>';
    }
    return $html;
}

//function to insert to devis_broker table 
function bindBrokerDevis($idBroker,$idDevis)
{
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "INSERT INTO `broker_devis`(`id_broker`, `id_devis`) VALUES ('$idBroker','$idDevis');";
    mysqli_query($cnx,$query);
    $last_id = mysqli_insert_id($cnx);
    return $last_id;
}

//insert to dossier table
function saveDossier($serv_id,$N_dossier){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "INSERT INTO `dossier`(`id_service`, `N_dossier`) VALUES ('$serv_id','$N_dossier')";
    mysqli_query($cnx,$query);
}

//function get All Dossier data
function getAllDossierData(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "CALL `sp_getAllDossier`();";
    $res = mysqli_query($cnx,$query);

    $row = mysqli_fetch_all($res);
    $html = '';
    $number = 1;
    foreach ($row as $val) {
        $html .= '<tr>';
        $html .= '<td>'.$number++.'</td>';
        $html .= '<td><a target="_blank" href="devis-show.php?id='.$val[0].'&client_id='.$val[1].'" title="Afficher Devis Detail">'.$val[2].'</a></td>';
        $html .= '<td>'.ucfirst($val[3]).'</td>';
        $html .= '<td>'.ucfirst($val[4]).'</td>';
        $html .= '<td>'.$val[5].'</td>';
        $html .= '<td><a href="dossier-show.php?s_id='.$val[6].'" class="btn btn-secondary btn-sm" title="Afficher Dossier detail" ><span><i class="bi bi-eye"></i></span></a></td>';
        $html .= '</tr>';
    
    }
    return $html;
}

//function to get the selected Dossier

function getSelectedDossier($service_id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }
    $query = "CALL `sp_getSelectedDossier`('".$service_id."');";
    $res = mysqli_query($cnx,$query);
    $row = mysqli_fetch_assoc($res);
    return $row;
}

//Fetching detail_devis based on devis_id

function getApprovedDevisDetails($devis_id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT * FROM `detail_devis` WHERE `id_devis`='$devis_id' AND `confirmed`='1'";
    $res = mysqli_query($cnx,$query);
    $rows = mysqli_fetch_all($res);
    return $rows;
}

function getDevisAllDetails($devis_id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT * FROM `detail_devis` WHERE `id_devis`='$devis_id'";
    $res = mysqli_query($cnx,$query);
    $services = array();
    while($row = mysqli_fetch_assoc($res)){
        $services[] = $row;
    }

    return $services;
}

//check if devis is bind to a broker

function checkBroker_devis($devis_id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT * FROM `broker_devis` WHERE `id_devis`='$devis_id'";
    $res = mysqli_query($cnx,$query);
    $rows_num = mysqli_num_rows($res);
    return $rows_num;
}

// Fetching broker_devis Data

function getBroker_devisData($devis_id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT * FROM `broker_devis` WHERE `id_devis`='$devis_id'";
    $res = mysqli_query($cnx,$query);
    $row = mysqli_fetch_assoc($res);
    return $row;
}

// Fetching detail_broker_devis sum Prices based on broker_devis id

function getBroker_devisSumPrices($broker_devis_id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT SUM(`prix`) AS price FROM `detail_broker_devis` WHERE `id_broker_devis` = $broker_devis_id;";
    $res = mysqli_query($cnx,$query);
    $row = mysqli_fetch_assoc($res);

    return $row["price"];
}

// Update broker sold

function updateBrokerSold($id_broker, $sold){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "UPDATE `broker` SET `sold` = (`sold` + $sold) WHERE `id`='$id_broker'";
    mysqli_query($cnx,$query);

}

//get broker by id

function getBrokerById($broker_id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT * FROM `broker` WHERE `id`='$broker_id'";
    $res = mysqli_query($cnx,$query);
    $row = mysqli_fetch_assoc($res);
    return $row;
}

// Sum devis Services prices 
function getDevis_detailsSumPrices($devis_id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $services = getDevisAllDetails($devis_id);

    $total = 0;
    foreach ($services as $service) {
        $total += getSumDevisPrices($service['id']);
    }

    return $total;
}

//Category fetch all Data
function getSuppCatData(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT * FROM `supp_category`";
    $res = mysqli_query($cnx,$query);
    return $res;
}

// get category by id

function getSuppCatById($id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT * FROM `supp_category` WHERE `id`= '$id'";
    $res = mysqli_query($cnx,$query);
    $row = mysqli_fetch_assoc($res);
    return $row;
}

//get Supplier Data
function getSupplierData(){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT * FROM `supplier`";
    $res = mysqli_query($cnx,$query);
    return $res;
}


function getSupplierById($id){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "SELECT * FROM `supplier` WHERE `id`= '$id'";
    $res = mysqli_query($cnx,$query);
    $row = mysqli_fetch_assoc($res);
    return $row;
}

// update supplier sold
function updateSupplierSold($id,$sold){
    $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $query = "UPDATE `supplier` SET `sold`='$sold' WHERE `id`='$id'";
    mysqli_query($cnx,$query);
}

?>