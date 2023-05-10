<?php include 'includes/config.php';

$query = "SELECT * FROM client_entreprise WHERE delete_status='0'";

$res = mysqli_query($cnx,$query);
$count_all_rows = mysqli_num_rows($res);
if(isset($_POST['search']['value'])){
    $search_value = $_POST['search']['value'];
    $query .= " AND (nom like '%".$search_value."%' ";
    $query .= " OR ICE like '%".$search_value."%' ";
    $query .= " OR email like '%".$search_value."%' ";
    $query .= " OR tel like '%".$search_value."%' ";
    $query .= " OR address like '%".$search_value."%' )";
}

if(isset($_POST['order'])){
    $column = $_POST['order'][0]['column'];
    $order = $_POST['order'][0]['dir'];
    $query .= " ORDER BY '".$column."' ".$order;
}else{
    $query .= " ORDER BY id ASC";
}

if($_POST['length'] != -1){
    $start = $_POST['start'];
    $length = $_POST['length'];
    $query .= " LIMIT ".$start.", ". $length;
}

$data = array();

$run_query = mysqli_query($cnx,$query);
$filtered_rows = mysqli_num_rows($run_query);
$number = 1;
while($row = mysqli_fetch_assoc($run_query)){
    $subarray = array();
    $subarray[] = $number;
    $subarray[] = $row['nom'];
    $subarray[] = $row['ICE'];
    $subarray[] = $row['email'];
    $subarray[] = $row['tel'];
    $subarray[] = $row['address'];
    $subarray[] = '<a href="javascript:void(0);" data-id="'.$row['id'].'" class="btn btn-primary btn-sm selectEntrepBtn"><span>Select</span></a>';
    $data[] = $subarray;
    $number++;
}

$output = array(
    'data'=>$data,
    'draw'=>intval($_POST['draw']),
    'recordsTotal'=> $count_all_rows,
    'recordsFiltered'=>$filtered_rows,
);

echo json_encode($output);

?>