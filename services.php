<?php
include 'header.php';



if (isset($_SESSION["error"])) {
    echo $_SESSION["error"];
    unset($_SESSION["error"]);
}
?>

<div class="pagetitle">
    <h1>Ajouter un service</h1>
</div>
<section class="section">
    <form action="service-add.php" method="POST">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Service information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <input type="text" name="serviceTitle" id="serTitleTxt" class="form-control serTitleTxt" required placeholder="Title">
                                </div>
                            </div>
                            <!-- <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-text">
                                        
                                        MAD
                                    </div>
                                    <input type="number" min="0" step="0.01" name="servicePrice" id="serTitlePrix" class="form-control serTitlePrix" required placeholder="0.00">
                                </div>
                            </div> -->
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" name="serviceRef" id="serRef" class="form-control servRef" placeholder="Référence" required>
                                </div>
                                <p class="feedback text-danger ps-1">
                                    
                                </p>
                            </div>
                        </div>
                        <!-- <div class="row">
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 my-3">
                <input type="submit" name="submit" id="serv_add" value="Create Service" class="btn btn-success float-end" title="Créer Service">
            </div>
        </div>
    </form>
</section>



<?php include 'footer.php'; ?>