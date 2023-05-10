<?php
include 'header.php';


if(isset($_GET["p_id"])){
    $purchase_id = $_GET["p_id"];
    $query = "SELECT * FROM `purchase` WHERE `id`='$purchase_id';";
    $res = mysqli_query($cnx,$query);
    $row = mysqli_fetch_assoc($res);
}

?>


<div class="pagetitle">
    <h1>Modifier l'achat</h1>
</div>

<section class="section">
    <form action="purchase-update.php" method="POST">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Achat information</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="serviceText" class="form-label">Nom</label>
                                    <input type="text" class="form-control" name="purchaseName" id="serviceText" placeholder="Nom" required value="<?=$row["name"];?>">
                                    <input type="hidden" name="p_id" value="<?=$purchase_id;?>">
                                </div>
                                <!-- ********* -->
                                <div class="mb-3">
                                    <label for="purchasePrice" class="form-label">Prix</label>
                                    <input type="number" min="0" step="0.01" name="purchasePrice" id="purchasePrice" class="form-control" required placeholder="0.00" value="<?=$row["price"];?>">
                                </div>
                                <!-- ********* -->
                                <div class="mb-3">
                                    <label for="purchaseNote" class="form-label">Note</label>
                                    <textarea name="purchaseNote" id="purchaseNote" class="form-control" rows="3" required><?=$row['note'];?></textarea>
                                </div>
                                
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 my-3">
                <input type="submit" name="submit" value="Update Achat" class="btn btn-success float-end">
            </div>
        </div>
    </form>
</section>


<?php include 'footer.php'; ?>