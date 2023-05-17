<?php
include 'header.php';

// if(!$role->hasPerm('create client')){
//     echo "<script>window.location='page-error-404.php'</script>";exit();
// }
$request ="SELECT * FROM `broker`;";
$res = mysqli_query($cnx,$request);
?>

<div class="pagetitle">
    <h1>Ajouter un Maître d'ouvrage</h1>
</div>
<section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Information du Maître d'ouvrage</h5>
                        <!-- Tabs navs -->
                        <ul class="nav nav-tabs mb-3" id="client" role="tablist">

                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="indiv-tab" data-bs-toggle="tab" data-bs-target="#indiv-tabs" type="button" role="tab" aria-controls="indiv-tabs" aria-selected="true">Individuel</button>
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
                                <form action="add_cus.php" method="POST">
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
                                                <input type="email" name="email" id="emailCusTxt" class="form-control"  placeholder="Email">
                                            </div>
                                            <div class="input-group mb-3">
                                                <input type="text" name="address" id="adrCusTxt" class="form-control"  placeholder="Address">
                                            </div>
                                            <!-- start of New Options -->

                                            <!-- <div class="my-4">
                                                <input type="checkbox" id="brokerCheckBox" class="form-check-input">
                                                <label for="brokerCheckBox"> &nbsp; Inclure un intermédiaire</label>
                                            </div> -->
                                            <div id="broker_div">
                                                <div class="input-group mb-3">
                                                    <select name="broker" id="brokerSelect" class="form-control" onchange="populateInput()">
                                                    <option value="" selected disabled>veuillez sélectionner l'intermédiaire</option>
                                                    <?php  foreach($res as $broker){ ?>
                                                        <option value="<?php echo $broker['phone']; ?>"><?php echo $broker['nom'] . ' ' . $broker['prenom']; ?></option>
                                                    <?php } ?>
                                                    </select>
                                                </div>
                                                <!-- <div>
                                                    <div class="input-group mb-3">
                                                    <div class="input-group-text">
                                                        <i class="bi bi-telephone-fill"></i>
                                                    </div>
                                                        <input type="number" min="0" name="brokerTel" id="brokerTel" class="form-control" required placeholder="intermédiaire Telephone">
                                                    </div>
                                                </div> -->
                                            </div>

                                            <!-- end of New Options -->
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
                                            <div class="col-md-12 mt-4">
                                                <input type="submit" name="submit" id="cus_add" value="Créer un Maître d'ouvrage" class="btn btn-outline-success fw-bold float-end" title="Créer un Maître d'ouvrage ">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <!-- Indivdual Client Form Elements END -->
                            </div>
                            <div class="tab-pane fade" id="entrep-tabs" role="tabpanel" aria-labelledby="entrep-tab">
                                <!-- Entreprise Client Form Elements -->
                                <form action="add_cus.php" method="POST">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group mb-3">
                                                <input type="text" name="entNom" id="nomEntTxt" class="form-control" required placeholder="Nom">
                                            </div>
                                            <div class="input-group mb-3">
                                                <input type="text" name="ICE" id="iceEntTxt" class="form-control" required placeholder="ICE">
                                            </div>
                                            <div class="input-group mb-3">
                                                <input type="text" name="entPhone" id="telEntTxt" class="form-control" required placeholder="Telephone">
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
                                            <!-- start of the new option -->
                                            <div id="broker_div">
                                                <div class="input-group mb-3">
                                                    <select name="broker" id="brokerSelected" class="form-control" onchange="populateInput2()">
                                                    <option value="" selected disabled>veuillez sélectionner l'intermédiaire</option>
                                                    <?php  foreach($res as $broker){ ?>
                                                        <option value="<?php echo $broker['phone']; ?>"><?php echo $broker['nom'] . ' ' . $broker['prenom']; ?></option>
                                                    <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- end of the new option -->
                                            <div class="mb-3">
                                                <div class="form-check" style="display:none">
                                                    <input class="form-check-input" type="radio" name="client-type" id="indivRadioBtn" value="individual"   >
                                                    <label class="form-check-label" for="indivRadioBtn">
                                                    Individuel  
                                                    </label>
                                                </div>
                                                <div class="form-check" style="display:none">
                                                    <input class="form-check-input" type="radio" name="client-type" id="entrepRadioBtn" value="entreprise" checked >
                                                    <label class="form-check-label" for="entrepRadioBtn">
                                                        Entreprise
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-4">
                                                <input type="submit" name="submit" id="cus_add" value="Créer un Maître d'ouvrage" class="btn btn-outline-success fw-bold float-end" title="Créer un Maître d'ouvrage">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <!-- Entreprise Client Form Elements END -->
                        <!-- Tabs content -->
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="row">
            <div class="col-md-12 my-3">
                <input type="submit" name="submit" id="cus_add" value="Create Customer" class="btn btn-outline-success fw-bold float-end" title="Créer un Client">
            </div>
        </div> -->
    </form>
</section>
<script>
    // for individual client
    function populateInput() {
        var select = document.getElementById("brokerSelect");
        var input = document.getElementById("telCusTxt");
        var selectedOption = select.options[select.selectedIndex];
        input.value = selectedOption.value;
    }
    //for entreprise client
    function populateInput2() {
        var select = document.getElementById("brokerSelected");
        var input = document.getElementById("telEntTxt");
        var selectedOption = select.options[select.selectedIndex];
        input.value = selectedOption.value;
    }
</script>
<?php include 'footer.php'; ?>