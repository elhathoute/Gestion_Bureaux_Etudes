<?php
include 'header.php';


if(isset($_SESSION["success"])){
    echo $_SESSION["success"];
    unset($_SESSION["success"]);
}

?>

<div class="row py-4">
    <div class="col-md-4">
        <a href='payment-create.php' id="addPaymentP" class="btn btn-outline-primary btn-invoice-payment <?= ($role->hasPerm('create payment')) ? "":"hide-element" ?>" title="Ajouter Paiement">
            <i class="bi bi-bank"></i>&nbsp;Ajouter des Paiements
        </a>
    </div>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <?php if (isset($_GET['message'])) {?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                $message = "<strong>Le paiement</strong> a réussi !";
                echo '<div class="success-message">' . $message . '</div>';
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php }?>
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Information des Paiements </h3>
                    <div class="tab-content" id="services-content">
                        <!-- table payment -->
                        <div class="overflow-auto">
                            <div>
                                <table id="paymentInfoTable" class="table table-hover table-bordered table-striped" style="width:100%">
                                    <thead>
                                    <tr>
                                            <th>
                                                <a href="#">Type</a>
                                            </th>
                                            <th>
                                                <a href="#">Reçu N°</a>
                                            </th>
                                            <th>
                                                <a href="#">Entré par</a>
                                            </th>
                                            <th>
                                                <a href="#">Payé Par</a>
                                            </th>
                                            <th>
                                                <a href="#">Date</a>
                                            </th>
                                            <th>
                                                <a href="#">Montant</a>
                                            </th>
                                            <th>
                                                <a href="#"></a>
                                            </th>
                                            
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php  echo getPaymentsInfo(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- table payment END -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<?php include 'footer.php'; ?>