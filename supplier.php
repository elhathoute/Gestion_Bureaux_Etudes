<?php
include 'header.php';


if(isset($_SESSION["error"])){
    echo $_SESSION["error"];
    unset($_SESSION["error"]);
}


?>

<div class="pagetitle">
    <h1>Ajouter un Fournisseur</h1>
</div>
<section class="section">
    <form  action="supplier-add.php" method="POST" id="createSupplierForm">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Fournisseur information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <input type="text" name="supplierFullName" id="supplierFullName" class="form-control" required placeholder="Nom Complet">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <input type="text" name="supplierAdr" id="supplierAdr" class="form-control" placeholder="Adresse">
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-text">
                                        <i class="bi bi-telephone-fill"></i>
                                    </div>
                                    <input type="number" min="0" name="supplierPhone" id="supplierPhone" class="form-control" required placeholder="Telephone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <select class="form-select" name="suppCatSelect" id="suppCatSelect" required>
                                    <option selected disabled value="">Sélectionner Categorie</option>
                                    <?php
                                        $res = getSuppCatData();
                                        while($row=mysqli_fetch_assoc($res)){
                                            echo '<option value="'.$row['id'].'">'.ucfirst($row['title']).'</option>';
                                        }
                                    
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 my-3">
                <input type="submit" name="submit" id="supplier_add" value="Créer Fournisseur" class="btn btn-success float-end" title="Créer Fournisseur">
            </div>
        </div>
    </form>
</section>


<?php include 'footer.php'; ?>