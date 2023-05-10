<?php
include 'header.php';

if(isset($_SESSION["success"])){
    echo $_SESSION["success"];
    unset($_SESSION["success"]);
}


?>

<div class="pagetitle">
    <h1>Liste des fournisseurs</h1>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Fournisseur information</h3>
                    <div class="tab-content" id="category-content">
                        <!-- table Catégories -->
                        <div class="overflow-auto">
                            <div>
                                <table id="supplierTable" class="table table-hover table-bordered table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>
                                                <a href="#">N°</a>
                                            </th>
                                            <th>
                                                <a href="#">Nom</a>
                                            </th>
                                            <th>
                                                <a href="#">Téléphone</a>
                                            </th>
                                            <th>
                                                <a href="#">Addresse</a>
                                            </th>
                                            <th>
                                                <a href="#">Categorie</a>
                                            </th>
                                            <th>
                                                <a href="#">Solde</a>
                                            </th>
                                            <th>
                                                <a href="#">Action</a>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $res = getSupplierData();
                                            //TODO add permissions to Catégories
                                            // $check_edit =  ($role->hasPerm('edit broker')) ? "":"hide-element";
                                            // $check_delete = ($role->hasPerm('delete broker')) ? "":"hide-element";
                                            $number = 1;
                                            $html = '';
                                            while($row = mysqli_fetch_assoc($res)){
                                                $category = ucfirst(getSuppCatById($row['cat_id'])['title']);
                                                $html .= '<tr>';
                                                $html .= '<td>'.$number.'</td>';
                                                $html .= '<td>'.$row['full_name'].'</td>';
                                                $html .= '<td>'.$row['phone'].'</td>';
                                                $html .= '<td>'.$row['address'].'</td>';
                                                $html .= '<td>'.$category.'</td>';
                                                $html .= '<td>'.$row['sold'].'</td>';
                                                $html .= '  <td>
                                                                <a href="javascript:void(0);" data-id="'.$row['id'].'" class="btn btn-primary btn-sm editSupplierBtn '.$check_edit.' " title="Modifier Fournisseur" ><span><i class="bi bi-pencil-square"></i></span></a>
                                                                <a href = "javascript:void(0);" data-id="'.$row['id'].'"  class=" btn btn-danger btn-sm deleteSupplierBtn '.$check_delete.' " title="Supprimer Fournisseur" ><span><i class="bi bi-trash"></i></span></a>
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
<div class="modal fade" id="editSupplierModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Update Fournisseur</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="javascript:void(0)" id="editSupplierForm" method="POST">

                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="">
                    <input type="hidden" name="tr_id" id="tr_id" value="">

                    <div class="mb-3">
                        <div class="input-group mb-3">
                            <input type="text" name="supplierFullName" id="supplierFullName" class="form-control" required placeholder="Nom Complet">
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="input-group mb-3">
                            <input type="text" name="supplierAdr" id="supplierAdr" class="form-control" placeholder="Adresse">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="input-group mb-3">
                            <div class="input-group-text">
                                <i class="bi bi-telephone-fill"></i>
                            </div>
                            <input type="number" min="0" name="supplierPhone" id="supplierPhone" class="form-control" required placeholder="Telephone">
                        </div>
                    </div>
                    <div class="mb-3">
                        <select class="form-select" name="suppCatSelect" id="suppCatSelect" required>
                            <option selected disabled value="">Sélectionner la Categorie</option>
                            <?php
                                $res = getSuppCatData();
                                while($row=mysqli_fetch_assoc($res)){
                                    echo '<option value="'.$row['id'].'">'.ucfirst($row['title']).'</option>';
                                }
                            
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" name="submit" class="btn btn-primary">Modifier</button>
                </div>

            </form>
        </div>
    </div>
</div>
<!-- edit Catégories modal end -->
<!-- Delete Catégories modal -->
<div class="modal fade" id="deleteSupplierModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class='modal-body'>
                <p class="text-center">Êtes-vous sûr de vouloir supprimer ce fournisseur ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger deleteSupplierModalBtn">Delete</button>
            </div>
        </div>
    </div>
</div>
<!-- Delete Catégories modal end -->
<?php include 'footer.php'; ?>