<?php
include 'header.php';

if(isset($_GET["sc"])){
    $msg='';
    if($_GET["sc"]=="sucadd"){
        $msg = '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i>&nbsp;
        <strong>Devis Added Successfully.</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    }elseif ($_GET["sc"]=="sucupd") {
        $msg = '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i>&nbsp;
        <strong>Devis Updated Successfully.</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    }
    
    $_GET['sc'] = $msg;
    echo $_GET['sc'];
    unset($_GET['sc']);
}


?>

<div class="pagetitle">
    <h1>Devis List</h1>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title"> informations des Devis</h3>
                    <div class="tab-content" id="services-content">
                        <!-- table Devis -->
                        <div class="overflow-auto">
                            <div>
                                <table id="devisTable" class="table table-hover table-bordered table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>
                                                <a href="#">N°</a>
                                            </th>
                                            <th>
                                                <a href="#">Numéro Devis</a>
                                            </th>
                                            <th>
                                                <a href="#">Client</a>
                                            </th>
                                            <th>
                                                <a href="#">Objet</a>
                                            </th>
                                            <th>
                                                <a href="#">Date creation</a>
                                            </th>
                                            <th>
                                                <a href="#">Net Total</a>
                                            </th>
                                            <th>
                                                <a href="#">status</a>
                                            </th>
                                            <th>
                                                <a href="#">Action</a>
                                            </th>
                                            
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $query = "CALL `sp_getDevis`();";
                                            $res = mysqli_query($cnx,$query);
                                            $number = 1;
                                            $check_edit =  ($role->hasPerm('edit devis')) ? "":"hide-element";
                                          
                                            $check_delete = ($role->hasPerm('delete devis')) ? "":"hide-element";
                                            while($row=mysqli_fetch_assoc($res)){
                                                // var_dump($row);
                                                // $check_client = ($row['status']=="accepter" && strtolower($row['type'])=="approved")? '<span><i class="bi bi-check-circle btn btn-outline-success btn-sm rounded-circle btn-client-approve" data-id="'.$row['id'].'" ></i></span> ' :'';//<span><i class="bi bi-x-circle btn btn-outline-danger btn-sm rounded-circle btn-cancel-client-approve" data-id="'.$row['id'].'" ></i></span>
                                                // $check_client = '';
                                                // if($row['status']=="accepter" && strtolower($row['type'])=="approved"){
                                                //     $check_client = '<span><i class="bi bi-check-circle btn btn-outline-success btn-sm rounded-circle btn-client-approve" data-id="'.$row['id'].'" title="Devis Approuvé par Client" ></i></span> ';
                                                //     if($row['client_approve']){
                                                //         $check_client = '<span><i class="bi bi-x-circle btn btn-outline-danger btn-sm rounded-circle btn-cancel-client-approve" data-id="'.$row['id'].'" title="Annuler l\'approbation" ></i></span>';
                                                //     }
                                                // }
                                                // $client_approve = ($row['client_approve']=="1")? "approved_row" :"";
                                                echo '<tr>
                                                    <td>'.$number.'</td>
                                                    <td>'.$row["number"].'</td>
                                                    <td>'.$row["client"].'</td>
                                                    <td>'.$row["objet"].'</td>
                                                    <td >'.$row["date_creation"].'</td>
                                                    <td >'.$row["net_total"].' DH</td>
                                                    <td><span class="'.styleStatus($row["status"]).'">'.$row["status"].'</span></td>
                                                    <td>
                                                        <a href="devis-show.php?id='.$row['id'].'&client_id='.$row['client_id'].'" data-id="'.$row['id'].'" data-id_client="'.$row['client_id'].'" class="btn btn-secondary btn-sm viewDevisBtn" title="Afficher Devis" ><span><i class="bi bi-eye"></i></span></a>
                                                        <a href="devis-edit.php?id='.$row['id'].'&client_id='.$row['client_id'].'" data-id="'.$row['id'].'" class="btn btn-primary btn-sm editDevisBtn  '.$check_edit.' " title="Modifier Devis" ><span><i class="bi bi-pencil-square"></i></span></a>
                                                        <a href="javascript:void(0)" data-id="'.$row['id'].'"  class=" btn btn-danger btn-sm deleteDevisBtn '.$check_delete.' " title="Supprimer Devis" ><span><i class="bi bi-trash"></i></span></a>
                                                        
                                                    </td>
                                                </tr>';
                                                $number++;
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- table Devis END -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Delete service modal -->
<div class="modal fade" id="deleteDevisModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class='modal-body'>
                <p class="text-center">Êtes-vous sûr de vouloir supprimer ce Devis ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-danger deleteDevisModalBtn">Supprimer</button>
            </div>
        </div>
    </div>
</div>
<!-- Delete service modal end -->

<?php include 'footer.php'; ?>