<?php
include 'header.php';


$query = "SELECT * FROM `client` WHERE 'remove'=0";
$clientRes = mysqli_query($cnx, $query);
?>

<div class="row py-4">
    <div class="col-md-4">
        <a href='javascript:void(0)' class="btn btn-outline-secondary btnChangePaymentClient" title="Sélectionner un autre Client">
            <i class="bi bi-plus-circle"></i>&nbsp;Change client
        </a>
    </div>
</div>

<section class="section">
    <div class="row">
        <form action="payment-add.php" id="invoicePaymentClient" method="POST" >
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Paiements pour Client</h3>
                        <div class="tab-content" id="services-content">
                            <!-- table payment -->
                            <div class="overflow-auto">
                                <div>
                                    <table id="paymentByClientTable" class="table table-hover table-bordered table-striped" style="width:100%">
                                        <thead>
                                            <!-- <tr>
                                                <th>
                                                    <a href="#">N°</a>
                                                </th>
                                                <th>
                                                    <a href="#">Client</a>
                                                </th>
                                                <th>
                                                    <a href="#">Description</a>
                                                </th>
                                                <th>
                                                    <a href="#">Date</a>
                                                </th>
                                                <th>
                                                    <a href="#">Total</a>
                                                </th>
                                                <th>
                                                    <a href="#">Avance</a>
                                                </th>
                                                <th>
                                                    <a href="#"></a>
                                                </th>


                                            </tr> -->
                                            <tr>
                                                <th>
                                                    <a href="#">Devis</a>
                                                </th>
                                                <th>
                                                    <a href="#">Client</a>
                                                </th>
                                                <th>
                                                    <a href="#">Ref</a>
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
                                                    <a href="#">Action</a>
                                                </th>


                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                            <!-- <tr>
                                                <td>001/2022</td>
                                                <td>aze...</td>
                                                <td></td>
                                                <td>2021/01/12</td>
                                                <td>920.00 DH</td>
                                                <td>800.00 DH</td>
                                                <td><input type="checkbox" name="" class="CBPaymentByClient" value="3"></td>
                                            </tr> -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- table payment END -->
                        </div>
                        <!-- payment -->
                        <div class="row my-3">
                            <div class="col-md-7"></div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <div class="row my-2 align-items-center">
                                        <div class="col-sm-6">
                                            <label class="fw-bold">Total:</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="labelClientPaymentTotal float-end fs-4 fw-bold" id="labelClientPaymentTotal">0.00 DH</label>
                                            <input type="hidden" name="" id="hiddenTotal">
                                            <input type="hidden" name="hiddenTotalValue" id="hiddenTotalValue">
                                            <input type="hidden" name="clientId" id="clientId">
                                            <input type="hidden" name="filter_type" id="filter_type">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="paymentClientPrice" class="form-label">Montant</label>
                                    <input type="number" step="0.01" class="form-control" name="paymentClientPrice" id="paymentClientPrice" placeholder="Prix" required>
                                </div>
                                <div class="mb-3">
                                    <label for="payment_giver" class="form-label">De</label>
                                    <input type="text" class="form-control" name="payment_giver" id="payment_giver" placeholder="Le Nom du Donateur" required>
                                </div>
                                <div class="mb-3">
                                    <label for="payment-method" class="form-label">Mode de paiement</label>
                                    <select name="payment-method"  class="form-select py-1 payment-method" id="payment-method">
                                        <option value="" selected disabled>Choisissez un mode de paiement</option>
                                        <option value="Espéce">Espéce</option>
                                        <option value="Check">Check</option>
                                        <option value="Virement">Virement</option>
                                        <option value="Trita">Traite</option>
                                    </select>
                                </div>

                                <div class="form-check my-3">
                                    <input class="form-check-input" type="checkbox" value="" id="supplierCheckbox" name="supplierCheckbox">
                                    <label class="form-check-label" for="supplierCheckbox">
                                        Inclure un Fournisseur
                                    </label>
                                </div>

                                <div class="supplierContainer" style="display: none;">

                                    <div class="mb-3">
                                        <label for="supplierSelect" class="form-label">Fournisseur</label>
                                        <select name="supplier"  class="form-select py-1" id="supplierSelect">
                                            <option value="" selected disabled>Choisissez un Fournisseur</option>
                                            <?php
                                                $res = getSupplierData();
                                                while($row=mysqli_fetch_assoc($res)){
                                                    echo '<option value="'.$row['id'].'">'.ucfirst($row['full_name']).'</option>';
                                                }
                                            
                                            ?>
                                        </select>
                                    </div>
    
                                    <div class="mb-3">
                                        <label for="paymentSupplier" class="form-label">Prix</label>
                                        <input type="number" step="0.01" class="form-control" name="paymentSupplier" id="paymentSupplier" placeholder="Prix"  >
                                    </div>
                                </div>


                                <button type="submit" id="pay_inv" class="btn btn-success float-end btnPayClientInvoice mt-5">Submit</button>
                            </div>
                        </div>

                        <!-- payment END -->

                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<div class="row">
    <div class="col-md-12 my-1">
        <a href='payments.php' class="btn btn-secondary float-start"><i class="bi bi-caret-left"></i> Retour</a>
    </div>
</div>

<!-- search client modal -->
<div class="modal fade" id="paymentByClientModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Modal title</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 client-select-container">
                    <label for="selectClientModal" class="form-label">Client</label>
                    <select id="selectClientModal" class="form-select">
                        <option value="" selected disabled>Choisir un Client</option>
                        <?php
                        while ($row = mysqli_fetch_assoc($clientRes)) {
                            $clientName = fetchClientName($row['type'], $row['id_client']);
                            echo '<option value=' . $row["id"] . '>' . $clientName . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="broker-select-container" style="display: none;">
                    <label for="selectBrokerModal" class="form-label">Intermédiaire</label>
                    <select id="selectBrokerModal" class="form-select">
                        <option value="" selected disabled>Choisir un Intermédiaire</option>
                        <?php
                        $query = "SELECT * FROM `broker`";
                        $brokerRes = mysqli_query($cnx, $query);
                        while ($row = mysqli_fetch_assoc($brokerRes)) {
                            echo '<option value=' . $row["id"] . '>' . ucfirst($row['prenom']). ' ' . strtoupper($row['nom'])  . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="my-4">
                    <a href="javascript:void(0)" id="btnFilterWith" class="float-end fs-6">Filtrer avec l'intermédiaire</a>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                <button type="button" class="btn btn-primary" id='searchClientPayment' title="Recherche"><i class="bi bi-search"></i> chercher</button>
            </div>
        </div>
    </div>
</div>
<!-- search client modal END -->
<?php include 'footer.php'; ?>