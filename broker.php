<?php
include 'header.php';


if(isset($_SESSION["error"])){
    echo $_SESSION["error"];
    unset($_SESSION["error"]);
}


?>

<div class="pagetitle">
    <h1>Ajouter intermédiaire</h1>
</div>
<section class="section">
    <form  action="broker-add.php" method="POST" id="createBrokerForm">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Informations de l'intermédiaire </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <input type="text" name="brokerNom" id="brokerNom" class="form-control" required placeholder="Nom">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <input type="text" name="brokerPrenom" id="brokerPrenom" class="form-control" required placeholder="Prenom">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-text">
                                        <i class="bi bi-telephone-fill"></i>
                                    </div>
                                    <input type="number" min="0" name="brokerTel" id="brokerTel" class="form-control" required placeholder="Telephone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" name="brokerIce" id="brokerIce" class="form-control" placeholder="ICE" >
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input type="text" name="brokerAdr" id="brokerAdr" class="form-control" placeholder="Address" >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 my-3">
                <input type="submit" name="submit" id="broker_add" value="Créer Intermédiaire" class="btn btn-success float-end" title="Créer Intermédiaire">
            </div>
        </div>
    </form>
</section>


<?php include 'footer.php'; ?>