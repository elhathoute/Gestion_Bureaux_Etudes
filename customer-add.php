<?php
include 'header.php';

// if(!$role->hasPerm('create client')){
//     echo "<script>window.location='page-error-404.php'</script>";exit();
// }

?>

<div class="pagetitle">
    <h1>Ajouter un client</h1>
</div>
<section class="section">
    <form action="add_cus.php" method="POST">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Information du client</h5>
                        <!-- Tabs navs -->
                        <ul class="nav nav-tabs mb-3" id="client" role="tablist">

                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="indiv-tab" data-bs-toggle="tab" data-bs-target="#indiv-tabs" type="button" role="tab" aria-controls="indiv-tabs" aria-selected="true">Individual</button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link " id="entrep-tab" data-bs-toggle="tab" data-bs-target="#entrep-tabs" type="button" role="tab" aria-controls="entrep-tabs" aria-selected="false">Entreprise</button>
                            </li>

                        </ul>
                        <!-- Tabs navs -->

                        <!-- Tabs content -->
                        <div class="tab-content" id="client-content">
                            <div class="tab-pane fade show active" id="indiv-tabs" role="tabpanel" aria-labelledby="indiv-tab">
                                <!-- Indivdual Client Form Elements -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <input type="text" name="prenom" id="prnCusTxt" class="form-control" required placeholder="Prenom">
                                        </div>
                                        <div class="input-group mb-3">
                                            <input type="text" name="nom" id="nomCusTxt" class="form-control" required placeholder="Nom">
                                        </div>
                                        <div class="input-group mb-3">
                                            <input type="text" name="phone" id="telCusTxt" class="form-control" required placeholder="Telephone">
                                        </div>

                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <div class="input-group-text">
                                                <i class="bi bi-envelope-fill"></i>
                                            </div>
                                            <input type="email" name="email" id="emailCusTxt" class="form-control" required placeholder="Email">
                                        </div>
                                        <div class="input-group mb-3">
                                            <input type="text" name="address" id="adrCusTxt" class="form-control" required placeholder="Address">
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check" style="display:none">
                                                <input class="form-check-input" type="radio" name="client-type" id="indivRadioBtn" value="individual" checked  >
                                                <label class="form-check-label" for="indivRadioBtn">
                                                Individuel  
                                                </label>
                                            </div>
                                            <div class="form-check" style="display:none">
                                                <input class="form-check-input" type="radio" name="client-type" id="entrepRadioBtn" value="entreprise" >
                                                <label class="form-check-label" for="entrepRadioBtn">
                                                    Entreprise
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Indivdual Client Form Elements END -->
                            </div>
                            <div class="tab-pane fade" id="entrep-tabs" role="tabpanel" aria-labelledby="entrep-tab">
                                <!-- Entreprise Client Form Elements -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <input type="text" name="entNom" id="nomEntTxt" class="form-control"  placeholder="Nom">
                                        </div>
                                        <div class="input-group mb-3">
                                            <input type="text" name="ICE" id="iceEntTxt" class="form-control"  placeholder="ICE">
                                        </div>
                                        <div class="input-group mb-3">
                                            <input type="text" name="entPhone" id="telEntTxt" class="form-control"  placeholder="Telephone">
                                        </div>

                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <div class="input-group-text">
                                                <i class="bi bi-envelope-fill"></i>
                                            </div>
                                            <input type="email" name="entEmail" id="emailEntTxt" class="form-control"  placeholder="Email">
                                        </div>
                                        <div class="input-group mb-3">
                                            <input type="text" name="entAddress" id="adrEntTxt" class="form-control"  placeholder="Address">
                                        </div>
                                    </div>
                                </div>
                                <!-- Entreprise Client Form Elements END -->
                            </div>

                        </div>
                        <!-- Tabs content -->



                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 my-3">
                <input type="submit" name="submit" id="cus_add" value="Create Customer" class="btn btn-success float-end" title="Créer un Client">
            </div>
        </div>
    </form>
</section>




<?php include 'footer.php'; ?>