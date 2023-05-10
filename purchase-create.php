<?php
include 'header.php';

?>


<div class="pagetitle">
    <h1>Ajouter un achat</h1>
</div>

<section class="section">
    <form action="purchase-add.php" method="POST">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Achat information</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="serviceText" class="form-label">Nom</label>
                                    <input type="text" class="form-control" name="purchaseName" id="serviceText" placeholder="Nom" required>
                                </div>
                                <!-- ********* -->
                                <div class="mb-3">
                                    <label for="purchasePrice" class="form-label">Prix</label>
                                    <input type="number" min="0" step="0.01" name="purchasePrice" id="purchasePrice" class="form-control" required placeholder="0.00">
                                </div>
                                <!-- ********* -->
                                <div class="mb-3">
                                    <label for="purchaseNote" class="form-label">Note</label>
                                    <textarea name="purchaseNote" id="purchaseNote" class="form-control" rows="3" required></textarea>
                                </div>
                                
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 my-3">
                <input type="submit" name="submit" id="pur_add" value="Create Achat" class="btn btn-success float-end" title="Créer Achat">
            </div>
        </div>
    </form>
</section>


<?php include 'footer.php'; ?>