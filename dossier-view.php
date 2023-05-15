<?php
include 'header.php';

$brokerRes = getBrokerData();


?>

<div class="pagetitle">
    <h1>Liste des dossiers</h1>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card rounded-4">
                <div class="card-body">
                    <div class="card-title py-2">Choisir un intermédiaire: </div>
                    <div class="row">
                        <div class="col-md-10 my-1">
                            <select class="form-select" id="brokerSelect">
                                <option value="" selected disabled>Veuillez choisir un intermédiaire </option>
                                <?php
                                    while ($row = mysqli_fetch_assoc($brokerRes)) {
                                        $brokerFullName = ucfirst($row["nom"]) .' '. ucfirst($row["prenom"]);
                                        echo '<option value=' . $row["id"] . '>' . $brokerFullName . '</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2 my-1">
                            <button id="dsResetBtn" class="btn btn-primary">Réinitialiser</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row my-2">
                        <div class="col-md-8">
                            <h3 class="card-title">Informations de dossier</h3>
                        </div>
                        
                    </div>



                    <div class="tab-content" id="services-content">
                        <!-- table Dossier -->
                        <div class="overflow-auto">
                            <div class="dsTableContent">
                                <table id="dossierTable" class="table table-hover table-bordered table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>
                                                <a href="#">N°</a>
                                            </th>
                                            <th>
                                                <a href="#">N°Devis</a>
                                            </th>
                                            <th>
                                                <a href="#">Objet</a>
                                            </th>
                                            <th>
                                                <a href="#">Nom du service</a>
                                            </th>
                                            <th>
                                                <a href="#">Date</a>
                                            </th>
                                            <th>
                                                <a href="#">Action</a>
                                            </th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?=getAllDossierData()?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- table Dossier END -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<?php include 'footer.php'; ?>