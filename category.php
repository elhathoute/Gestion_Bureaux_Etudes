<?php
include 'header.php';


if(isset($_SESSION["error"])){
    echo $_SESSION["error"];
    unset($_SESSION["error"]);
}


?>

<div class="pagetitle">
    <h1>Add Catégorie</h1>
</div>
<section class="section">
    <form  action="supp-category-add.php" method="POST" id="createSuppCategoryForm">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">information de la Catégorie </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <input type="text" name="catTitle" id="catTitle" class="form-control" required placeholder="Nom">
                                </div>
                            </div>
                            <div class="col-md-6">
                            <select class="form-select" aria-label="Default select example" name="catType" id="catTypeSelect" required>
                                <option selected disabled value=''>Sélectionner le type</option>
                                <option value="Bureau d'étude">Bureau d'étude</option>
                                <option value='Bureau de controle'>Bureau de contrôle</option>
                                    
                            </select>
                            <span id='catSelectError'></span>
                            </div>
                            
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 my-3">
                <input type="submit" name="submit" id="category_add" value="Créer Catégorie" class="btn btn-success float-end" title="Créer Catégorie">
            </div>
        </div>
    </form>
</section>


<?php include 'footer.php'; ?>