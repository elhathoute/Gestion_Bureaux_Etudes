<?php
include 'header.php';


if(isset($_SESSION["success"])){
    echo $_SESSION["success"];
    unset($_SESSION["success"]);
}
?>



<div class="pagetitle">
    <h1>Liste des Maîtres d'ouvrage </h1>
</div>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Informations des Maîtres d'ouvrage </h3>
                    <!-- Tabs navs -->
                    <ul class="nav nav-tabs mb-3" id="client" role="tablist">

                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="indiv-tab" data-bs-toggle="tab" data-bs-target="#indiv-tabs" type="button" role="tab" aria-controls="indiv-tabs" aria-selected="true">liste des individuelles</button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link " id="entrep-tab" data-bs-toggle="tab" data-bs-target="#entrep-tabs" type="button" role="tab" aria-controls="entrep-tabs" aria-selected="false">liste d'entreprises</button>
                        </li>

                    </ul>
                    <!-- Tabs navs END -->

                    <div class="tab-content mt-4 overflow-auto" id="client-content">
                        <div class="tab-pane fade show active" id="indiv-tabs" role="tabpanel" aria-labelledby="indiv-tab">
                            <!-- table Individual -->
                            <div class="">
                                <div>
                                    <table id="myTable" class="table table-hover table-bordered table-striped mt-5" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <a href="#">N°</a>
                                                </th>
                                                <th>
                                                    <a href="#">Prenom</a>
                                                </th>
                                                <th>
                                                    <a href="#">Nom</a>
                                                </th>
                                                <th>
                                                    <a href="#">Email</a>
                                                </th>
                                                <th>
                                                    <a href="#">Téléphone</a>
                                                </th>
                                              
                                                <th>
                                                    <a href="#">Address</a>
                                                </th>
                                                <th>
                                                    <a href="#">Action</a>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $res = getIndvClientData();
                                                $check_edit =  ($role->hasPerm('edit client')) ? "":"hide-element";
                                                $check_delete = ($role->hasPerm('delete client')) ? "":"hide-element";
                                                $number = 1;
                                                $html = '';
                                                while($row = mysqli_fetch_assoc($res)){
                                                    $html .= '<tr>';
                                                    $html .= '<td>'.$number.'</td>';
                                                    $html .= '<td>'.$row['prenom'].'</td>';
                                                    $html .= '<td>'.$row['nom'].'</td>';
                                                    $html .= '<td>'.$row['email'].'</td>';
                                                    $html .= '<td>'.$row['tel'].'</td>';
                                                    $html .= '<td>'.$row['address'].'</td>';
                                                    
                                                    $html .= '  <td>
                                                                    <a href="javascript:void(0);" data-id="'.$row['id'].'" class="btn btn-primary btn-sm editBtn '.$check_edit.' " data-bs-toggle="modal" data-bs-target="#EditCusModal" title="Modifier Maître ouvrage" ><span><i class="bi bi-pencil-square"></i></span></a>
                                                                    <a href = "javascript:void(0);" data-id="'.$row['id'].'"  class=" btn btn-danger btn-sm deleteBtn '.$check_delete.' " title="Supprimer  Maître ouvrage"><span><i class="bi bi-trash"></i></span></a>
                                                                </td>';
                                                    $html .= '</tr>';
                                                    $number++;
                                                }
                                                echo $html;
                                            
                                            
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- table Individual END -->
                        </div>
                        <!-- table entreprise -->
                        <div class="tab-pane fade" id="entrep-tabs" role="tabpanel" aria-labelledby="entrep-tab">
                            <div class="">
                                <div>
                                    <table id="entrepTable" class="table table-hover table-bordered table-striped mt-5" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <a href="#">N°</a>
                                                </th>
                                                <th>
                                                    <a href="#">Nom</a>
                                                </th>
                                                <th>
                                                    <a href="#">ICE</a>
                                                </th>
                                                <th>
                                                    <a href="#">Email</a>
                                                </th>
                                                <th>
                                                    <a href="#">Téléphone</a>
                                                </th>
                                               
                                                <th>
                                                    <a href="#">Address</a>
                                                </th>
                                                <th>
                                                    <a href="#">Action</a>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                                $res = getEntrepClientData();
                                                $check_edit =  ($role->hasPerm('edit client')) ? "":"hide-element";
                                                $check_delete = ($role->hasPerm('delete client')) ? "":"hide-element";
                                                $number = 1;
                                                $html = '';
                                                while($row = mysqli_fetch_assoc($res)){
                                                    $html .= '<tr>';
                                                    $html .= '<td>'.$number.'</td>';
                                                    $html .= '<td>'.$row['nom'].'</td>';
                                                    $html .= '<td>'.$row['ICE'].'</td>';
                                                    $html .= '<td>'.$row['email'].'</td>';
                                                    $html .= '<td>'.$row['tel'].'</td>';
                                                    $html .= '<td>'.$row['address'].'</td>';
                                                    
                                                    $html .= '  <td>
                                                                    <a href="javascript:void(0);" data-id="'.$row['id'].'" class="btn btn-primary btn-sm editEntrepBtn '. $check_edit.' " title="Modifier Maître ouvrage" ><span><i class="bi bi-pencil-square"></i></span></a>
                                                                    <a href = "javascript:void(0);" data-id="'.$row['id'].'"  class=" btn btn-danger btn-sm deleteEntrepBtn '.$check_delete.' " title="Supprimer Maître ouvrage" ><span><i class="bi bi-trash"></i></span></a>
                                                                </td>';
                                                    $html .= '</tr>';
                                                    $number++;
                                                }
                                                echo $html;
                                            
                                            
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- table entreprise end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- edit customer modal -->
<!-- Button trigger modal -->
<!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
    Launch static backdrop modal
</button> -->

<!-- Modal Individual-->
<div class="modal fade" id="EditCusModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">mettre à jour Maître d'ouvrage</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="javascript:void(0)" id="editCusForm" method="POST">

                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="">
                    <input type="hidden" name="tr_id" id="tr_id" value="">
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prenom</label>
                        <!-- <input type="text" class="form-control" name="prenom" id="prenom" placeholder="Prenom" required> -->
                        <input type="text" class="form-control" name="prenom" id="prenom" placeholder="Prenom" required>
                    </div>
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" name="nom" id="nom" placeholder="Nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" name="email" id="email" placeholder="Email">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Téléphone</label>
                        <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" name="address" id="address" placeholder="Address" >
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" name="submit" class="btn btn-primary">mettre à jour</button>
                </div>

            </form>
        </div>
    </div>
</div>
<!-- edit customer modal end -->
<!-- Delete modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class='modal-body'>
                <p class="text-center">Êtes-vous sûr de vouloir supprimer ce Maître d'ouvrage ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-danger deleteModalBtn">supprimer</button>
            </div>
        </div>
    </div>
</div>
<!-- Delete modal end -->
<!-- *************************** -->
<!-- Modal Entreprise-->
<div class="modal fade" id="EditCusEntrepModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">mettre à jour Maître d'ouvrage</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="javascript:void(0)" id="editCusEntrepForm" method="POST">

                <div class="modal-body">
                    <input type="hidden" name="id" id="id_ent" value="">
                    <input type="hidden" name="tr_id" id="tr_id_ent" value="">
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prenom</label>
                        <!-- <input type="text" class="form-control" name="prenom" id="prenom" placeholder="Prenom" required> -->
                        <input type="text" class="form-control" name="prenom" id="nom_ent" placeholder="Nom" required >
                    </div>
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" name="nom" id="ice" placeholder="Ice" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" name="email" id="email_ent" placeholder="Email" >
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Téléphone</label>
                        <input type="text" class="form-control" name="phone" id="phone_ent" placeholder="Phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" name="address" id="address_ent" placeholder="Address" reqired>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" name="submit" class="btn btn-primary">Mettre à jour</button>
                </div>

            </form>
        </div>
    </div>
</div>
<!-- edit customer modal end -->
<!-- Delete Entreprise modal -->
<div class="modal fade" id="deleteEntrepModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class='modal-body'>
                <p class="text-center">Êtes-vous sûr de vouloir supprimer ce Maître d'ouvrage ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-danger deleteEntrepModalBtn">Supprimer</button>
            </div>
        </div>
    </div>
</div>
<!-- Delete Entreprise modal end -->
<?php include 'footer.php'; ?>