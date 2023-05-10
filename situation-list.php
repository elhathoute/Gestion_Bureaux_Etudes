<?php
include 'header.php';

$query = "SELECT * FROM `client` WHERE 'remove'=0";
$clientRes = mysqli_query($cnx, $query);

?>

<div class="pagetitle">
    <h1>Liste des situations</h1>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card rounded-4">
                <div class="card-body">
                    <div class="card-title py-2">Choisir un client: </div>
                    <select class="form-select" id="situationSelect">
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
                        <div class="col-md-6">
                            <h3 class="card-title">Situation information</h3>
                        </div>
                        <div class="col-md-6 d-flex align-items-center justify-content-end" id="expSitBtn">
                            
                        </div>
                    </div>



                    <div class="tab-content" id="services-content">
                        <!-- table Situation -->
                        <div class="overflow-auto">
                            <div>
                                <table id="situationTable" class="table table-hover table-bordered table-striped" style="width:100%">
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
                                                <a href="#">Service</a>
                                            </th>
                                            <th>
                                                <a href="#">Prix</a>
                                            </th>
                                            <th>
                                                <a href="#">Avance</a>
                                            </th>
                                            <th>
                                                <a href="#">Status</a>
                                            </th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
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