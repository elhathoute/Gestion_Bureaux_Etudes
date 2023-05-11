<?php
include 'header.php';

if(isset($_SESSION["success"])){
    echo $_SESSION["success"];
    unset($_SESSION["success"]);
}


?>

<div class="pagetitle">
    <h1>Liste des catégories </h1>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Informations des Catégories </h3>
                    <div class="tab-content" id="category-content">
                        <!-- table Catégories -->
                        <div class="overflow-auto">
                            <div>
                                <table id="suppCatTable" class="table table-hover table-bordered table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>
                                                <a href="#">N°</a>
                                            </th>
                                            <th>
                                                <a href="#">Titre</a>
                                            </th>
                                            <th>
                                                <a href="#">Type</a>
                                            </th>
                                            <th>
                                                <a href="#">Action</a>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $res = getSuppCatData();
                                            //TODO add permissions to Catégories
                                            $check_edit =  ($role->hasPerm('edit broker')) ? "":"hide-element";
                                            $check_delete = ($role->hasPerm('delete broker')) ? "":"hide-element";
                                            $number = 1;
                                            $html = '';
                                            while($row = mysqli_fetch_assoc($res)){
                                                $html .= '<tr>';
                                                $html .= '<td>'.$number.'</td>';
                                                $html .= '<td>'.$row['title'].'</td>';
                                                $html .= '<td>'.$row['type'].'</td>';
                                                $html .= '  <td>
                                                                <a href="javascript:void(0);" data-id="'.$row['id'].'" class="btn btn-primary btn-sm editSuppCatBtn '.$check_edit.' " title="Modifier Catégorie" ><span><i class="bi bi-pencil-square"></i></span></a>
                                                                <a href = "javascript:void(0);" data-id="'.$row['id'].'"  class=" btn btn-danger btn-sm deleteSuppCatBtn '.$check_delete.' " title="Supprimer Catégorie" ><span><i class="bi bi-trash"></i></span></a>
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
                        <!-- table Catégories END -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Catégories-->
<div class="modal fade" id="editSuppCatModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">mettre à jour la Catégorie</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="javascript:void(0)" id="editSuppCatForm" method="POST">

                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="">
                    <input type="hidden" name="tr_id" id="tr_id" value="">
                    <div class="mb-3">
                        <label for="catTitle" class="form-label">Nom</label>
                        <input type="text" class="form-control" name="catTitle" id="catTitle" placeholder="Nom" required>
                    </div>
                    <div class="mb-3">
                        <select class="form-select" name="catType" id="catTypeSelect" required>
                            <option selected disabled value=''>Sélectionner le type</option>
                            <option value="Bureau d'étude">Bureau d'étude</option>
                            <option value='Bureau de contrôle'>Bureau de contrôle</option>
                                    
                        </select>
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
<!-- edit Catégories modal end -->
<!-- Delete Catégories modal -->
<div class="modal fade" id="deleteSuppCatModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class='modal-body'>
                <p class="text-center">Êtes-vous sûr de vouloir supprimer cette catégorie ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-danger deleteSuppCatModalBtn">Supprimer</button>
            </div>
        </div>
    </div>
</div>
<!-- Delete Catégories modal end -->
<?php include 'footer.php'; ?>