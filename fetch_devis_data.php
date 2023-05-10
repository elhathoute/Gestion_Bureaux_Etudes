<?php 
// include 'includes/config.php';

//   $query = "SELECT devis.id,devis.number,
// CASE WHEN client.type='individual' THEN (SELECT CONCAT(client_individual.prenom,' ',client_individual.nom)AS Client FROM client_individual WHERE client.id_client=client_individual.id) WHEN client.type='entreprise' THEN ((SELECT client_entreprise.nom FROM client_entreprise WHERE client.id_client=client_entreprise.id)) END AS client,devis.date_creation,devis.status FROM devis INNER JOIN client ON devis.id_client=client.id ";

// $res = mysqli_query($cnx,$query);
// $count_all_rows = mysqli_num_rows($res);
// if(isset($_POST['search']['value'])){
//     $search_value = $_POST['search']['value'];
//     $query .= " WHERE client_individual.prenom like '%".$search_value."%' ";
//     $query .= " OR client_individual.nom like '%".$search_value."%' ";
//     $query .= " OR client_entreprise.nom like '%".$search_value."%' ";
//     $query .= " OR devis.date_creation like '%".$search_value."%' ";
//     $query .= " OR devis.status like '%".$search_value."%' ";
//     $query .= " OR devis.number like '%".$search_value."%' ";

// }

// if(isset($_POST['order'])){
//     $query .= " ORDER BY devis.date_creation;";
//     // $column = $_POST['order'][0]['column'];
//     // $order = $_POST['order'][0]['dir'];
//     // $query .= " ORDER BY '".$column."' ".$order;
// }else{
//     // $query .= " ORDER BY devis.date_creation;";
// }

// // if($_POST['length'] != -1){
// //     $start = $_POST['start'];
// //     $length = $_POST['length'];
// //     $query .= " LIMIT ".$start.", ". $length;
// // }

// $data = array();

// $run_query = mysqli_query($cnx,$query);
// $filtered_rows = mysqli_num_rows($run_query);
// $number = 1;
// while($row = mysqli_fetch_assoc($run_query)){
//     $subarray = array();
//     $subarray[] = $number;
//     $subarray[] = $row['number'];
//     $subarray[] = $row['client'];
//     $subarray[] = $row['date_creation'];
//     $subarray[] = $row['status'];
//     $subarray[] = '<a href="javascript:void(0);" data-id="'.$row['id'].'" class="btn btn-primary btn-sm editServiceBtn"  ><span><i class="bi bi-pencil-square"></i></span></a>
//                     <a href = "javascript:void(0);" data-id="'.$row['id'].'"  class=" btn btn-danger btn-sm deleteServiceBtn"><span><i class="bi bi-trash"></i></span></a>';
//     $data[] = $subarray;
//     $number++;
// }

// $output = array(
//     'data'=>$data,
//     'draw'=>intval($_POST['draw']),
//     'recordsTotal'=> $count_all_rows,
//     'recordsFiltered'=>$filtered_rows,
// );

// echo json_encode($output);


//*******************************************NOT WORKING *********************************************
?>