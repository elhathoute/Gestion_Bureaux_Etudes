<?php 
include 'header.php';
?>
<div class="pagetitle">
    <h1>Caisse</h1>
</div>

<section class="section">
    <!-- <div class="row">
        <div class="col-lg-12">
            <div class="card rounded-4">
                <div class="card-body row">
                    <div class="card-title py-2">Filter: </div>
                    <div class="col-md-5 my-1">
                        <select class="form-select" id="situationSelect">
                            <option value="" selected disabled>Veuillez choisir un Maître d'ouvrage </option>
                        </select>
                    </div>
                    <div class="col-md-5 my-1">
                        <select class="form-select" id="brokerSelectsituation">
                            <option value="" selected disabled>Veuillez choisir un intermédiaire </option>
                        </select>
                    </div>
                    <div class="col-md-2 my-1">
                        <button class="situationreload btn btn-primary form-control">Réinitialiser</button>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <div class="row">
        <!-- <div class="mb-3 gap-2 d-flex">
            <div class="bg-white col-4 py-3 text-danger fw-bold fs-2 rounded text-center">Total non payé : <span class="" id="totalNonPayeView"></span>
                <div class="">
                    <span class="ps-5 fs-1" id="totalPriceNonPayeView"></span>
                </div>
            </div>
            <div class="bg-white col-4 py-3 text-success fw-bold fs-2 rounded text-center">Total payé : <span class="" id="totalPayeView"></span>
                <div class="">
                    <span class="ps-5 fs-1" id="totalPricePayeView"></span>
                </div>
            </div>
            <div class="bg-secondary bg-opacity-25 col-4 py-3 text-danger fw-bold fs-2 rounded text-center">Total : <span class="" id="totalAvanceView"></span>
                <div class="">
                    <span class="ps-5 fs-1" id="totalPriceAvanceView"></span>
                </div>
            </div>
        </div> -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row my-2">
                        <div class="col-md-6">
                            <h3 class="card-title">Caisse information</h3>
                        </div>
                    <!-- <form action="caiseDetails.php" method="POST" class="col-md-6 d-flex  justify-content-end"> -->
                        <div class="col-md-6 d-flex align-items-center justify-content-end" id="SearchField">
                            
                        </div>
                    <!-- </form> -->
                    </div>
                    <div class="tab-content" id="">
                        <!-- table Situation -->
                        <div class="overflow-auto">
                            <div>
                                <table id="caiseTable" class="table table-hover table-bordered table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>
                                                <a href="#">N°</a>
                                            </th>
                                            <th>
                                                <a href="#">Caise</a>
                                            </th>
                                            <!-- <th>
                                                <a href="#">Libellé</a>
                                            </th>
                                            <th>
                                                <a href="#">Crédit</a>
                                            </th>
                                            <th>
                                                <a href="#">Débit</a>
                                            </th>
                                            <th>
                                                <a href="#">Solde</a>
                                            </th> -->
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                    // echo getAllSituation();
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- table Situation END -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<?php include 'footer.php'; ?>

