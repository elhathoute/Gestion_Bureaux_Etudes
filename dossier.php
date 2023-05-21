<?php
include 'header.php';

$query = "SELECT * FROM `client` WHERE 'remove'=0";
$clientRes = mysqli_query($cnx, $query);

?>

<div class="pagetitle">
    <h1>Add Dossier </h1>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card rounded-4">
                <div class="card-body">
                    <div class="card-title py-2">Choisir un client: </div>
                    <select class="form-select" id="dossierClientSelect">
                        <option value="" selected disabled>Veuillez choisir un client </option>
                        <?php
                        while ($row = mysqli_fetch_assoc($clientRes)) {
                            $clientName = fetchClientName($row['type'], $row['id_client']);
                            echo '<option value=' . $row["id"] . '>' . $clientName . '</option>';
                        }
                        ?>
                    </select>
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
                            <h3 class="card-title">Dossier information</h3>
                        </div>
                        <!-- btn placeholder -->
                        <!-- <div class="col-md-4 d-flex align-items-center justify-content-end" id="expSitBtn">
                            
                        </div> -->
                    </div>

                    <div class="ds_content">

                        
                        <!-- table dossier -->
                        <div class="tab-content">
                            <div class="overflow-auto">
                                <div>
                                    <table id="dossierClTable" class="table table-hover table-bordered table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <a href="#">N°</a>
                                                </th>
                                                <th>
                                                    <a href="#">Devis</a>
                                                </th>
                                                <th>
                                                    <a href="#">Objet</a>
                                                </th>
                                                <th>
                                                    <a href="#">Date de Creation</a>
                                                </th>
                                                <th>
                                                    <a href="#">Action</a>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- table Dossier END -->

                        
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 my-3">
            <input type="button"  value="Générer dossier" id="btn_createDs" class="btn btn-success float-end invisible" title="Créer Dossier">
        </div>
    </div>
</section>


<!-- Modal devis Service-->
<div class="modal fade" id="showDvSrvDs" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Devis services</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>


            <div class="modal-body">
                <div class="list-group">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>


        </div>
    </div>
</div>
<!-- edit devis Service modal end -->


<?php include 'footer.php'; ?>