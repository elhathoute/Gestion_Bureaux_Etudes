<?php include 'includes/config.php';

//include class role and functions to check for user permession for edit and delete
include 'includes/autoloader.php';
include 'functions.php';
include 'check-role.php';


$query = "SELECT * FROM service ";

$res = mysqli_query($cnx,$query);
$count_all_rows = mysqli_num_rows($res);
if(isset($_POST['search']['value'])){
    $search_value = $_POST['search']['value'];
    $query .= " WHERE title like '%".$search_value."%' ";
    $query .= " OR prix like '%".$search_value."%' ";
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
    $subarray[] = $row['title'];
    $subarray[] = $row['prix'];

    $check_edit =  ($role->hasPerm('edit service')) ? "":"hide-element";
    $check_delete = ($role->hasPerm('delete service')) ? "":"hide-element";

    $subarray[] = '<a href="javascript:void(0);" data-id="'.$row['id'].'" class="btn btn-primary btn-sm editServiceBtn '.$check_edit.' "  ><span><i class="bi bi-pencil-square"></i></span></a>
                    <a href = "javascript:void(0);" data-id="'.$row['id'].'"  class=" btn btn-danger btn-sm deleteServiceBtn '.$check_delete.' "><span><i class="bi bi-trash"></i></span></a>';
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