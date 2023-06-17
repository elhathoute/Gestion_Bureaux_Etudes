<?php
include 'header.php';

$invoice = getSelectedInvoiceInfo();
$alert;
if($invoice['status']==strtolower('rejeter')){
    $alert = 'alert-danger';
}elseif($invoice['status']==strtolower('accepter')){
    $alert = 'alert-success';
}else{$alert = 'alert-warning';}

function br2nl($string)
{
    return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
}
?>

<div class="row my-3">
    <div class="pagetitle col-md-8">
        <h1>View Facture</h1>
    </div>
    
    <div class="col-md-4 <?= ($role->hasPerm('export invoice')) ? "":"hide-element" ?> <?= (strtoupper($invoice['type'])==strtoupper("Approved"))? '' :  "hide-element";?>">
    <?php if(isset($_GET['broker_id'])){?>
        <a target="_blank" href='invoice_export.php?id=<?=$_GET['id']?>&client_id=<?=$_GET['client_id']?>&broker_id=<?=$_GET['broker_id']?>' class="btn btn-danger float-end" title="Imprimer Facture"><i class="bi bi-download"></i> Export</a>
    <?php }else{?>
        <a target="_blank" href='invoice_export.php?id=<?=$_GET['id']?>&client_id=<?=$_GET['client_id']?>' class="btn btn-primary float-end" title="Imprimer Facture"><i class="bi bi-download"></i> Export</a>
    <?php }?>
    </div>
</div>
<div class="row">
    <div class="alert <?php echo $alert;?>" role="alert">
        <?php echo $invoice['status']; ?>
    </div>
</div>

<section class="section">
    <form action="" id="invoiceViewForm" method="POST">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mt-3 justify-content-between">
                            <div class="col-md-3">
                                <div class="invoice-logo">
                                    <img src="images/BeplanLogo.png" class="img-fluid" alt="Company Logo">
                                </div>
                            </div>
                            <!-- <div class="col-md-6"></div> -->
                            <div class="col-md-3">
                                <h2 class="h2 fw-bold text-dark float-end ">Facture</h2>
                            </div>
                        </div>
                        <div class="col-md-12 my-3">
                            <div class="h-line"></div>
                        </div>
                        <!-- invoice content -->
                        <!-- client section -->
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label class="fw-bold">De</label>
                                <div class="input-group mb-3">
                                    <input type="text" name="" class="form-control-plaintext p-0" value="BEPLAN" required placeholder="title" disabled>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label class="fw-bold">À</label>
                                <fieldset class="border-dashed">
                                    <div class="input-group">
                                        <input type="hidden" id="client_id" name="client_id" >
                                        <input type="text" class="form-control-plaintext fw-bold fs-6 py-0" value="<?php
                                        if(isset($_GET['broker_id'])){
                                            $broker=getBrokerById($_GET['broker_id']);
                                            echo strtoupper( $broker['nom'].' '.$broker['prenom']);
                                        }else{
                                            echo strtoupper(getSelectedClientName()); 
                                        }
                                        ?>" name="" id="receiverName" disabled>
                                    </div>
                                    <div class="input-group">
                                        <textarea  class="form-control-plaintext fs-6 py-0 " value="" name="" id="receiverAdr" style='resize: none;' disabled><?php 
                                            $adr_ice = explode('/',getSelectedClientAdr());
                                            if(count($adr_ice) > 1){
                                                $string = $adr_ice[0] . '<br>' . $adr_ice[1];
                                                echo br2nl($string); 
                                            }else{
                                                echo getSelectedClientAdr();
                                            }
                                        
                                        ?></textarea>
                                    </div>

                                    <!-- <div class="input-group">
                                        <input type="text" class="form-control-plaintext fs-6 py-0 " placeholder="Something..." name="" id="">
                                    </div> -->
                                </fieldset>
                                <!-- <div class="text-center py-1">
                                    <a href="javascript:void(0)" id="selectClientModal" class=""><i class="bi bi-box-arrow-up-right" style="font-size:12px"></i> Select client</a>
                                </div> -->
                            </div>
                            <div class="col-md-4">
                                <div class="row my-1">
                                    <label for="" class="col-sm-8 col-form-label fw-bold">N° de facture</label>
                                    <div class="col-sm-4 my-auto">
                                        <input type="text" class="form-control border-dashed p-0" id="invoice_number" name="invoice_number" value="<?php echo $invoice['F_number'];?>" disabled>
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <label for="" class="col-sm-5 col-form-label fw-bold">Date</label>
                                    <div class="col-sm-7 my-auto">
                                        <input type="date" class="form-control-plaintext " id="" value="<?php echo date('Y-m-d',strtotime($invoice['date_creation']));?>" disabled>
                                    </div>
                                </div>
                                <!-- <div class="row my-1">
                                    <label for="" class="col-sm-5 col-form-label fw-bold">Date d'échéance</label>
                                    <div class="col-sm-7 my-auto">
                                        <input type="date" class="form-control-plaintext " id="due_date" value="<?php echo date('Y-m-d',strtotime($invoice['due_date']));?>" disabled>
                                    </div>
                                </div> -->
                                <!-- <div class="row my-1">
                                    <label for="" class="col-sm-5 col-form-label fw-bold">Description</label>
                                    <div class="col-sm-7 my-auto">
                                        <textarea name="" id="" class="form-control border-dashed" rows="1">Projet name...</textarea>
                                    </div>
                                </div>
                                <div class="row my-1">
                                    <label for="" class="col-sm-5 col-form-label fw-bold">Projet</label>
                                    <div class="col-sm-7 my-auto">
                                        <textarea name="" id="" class="form-control border-dashed" rows="1">Projet name...</textarea>
                                    </div>
                                </div> -->
                            </div>
                            <div class="row my-3">
                                <label for="" class="col-sm-2 col-form-label fw-bold">Objet</label>
                                <div class="col-sm-10 my-auto">
                                    <textarea name="" id="objet_name" class="form-control border-dashed" rows="1" placeholder="Objet" required disabled><?= $invoice['objet']; ?></textarea>
                                </div>
                            </div>
                            <div class="row my-1">
                                <label for="" class="col-sm-2 col-form-label fw-bold">Sis à</label>
                                <div class="col-sm-10 my-auto">
                                    <textarea name="" id="sisTxt" class="form-control border-dashed" rows="1" placeholder="Sis à" required disabled><?= $invoice['located']; ?></textarea>
                                </div>
                            </div>
                            <!-- end client section -->
                            <!-- services Section -->
                            <div class="col-md-12 my-3">
                                <div class="h-line"></div>
                            </div>
                            <div class="row my-3">
                                <table class="table table-bordered table-hover table-striped servicesTable " id="invoiceShowTable">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th width="500">Service</th>
                                            <th>U</th>
                                            <th>Qte</th>
                                            <th>Prix</th>
                                            <th>Remise</th>
                                            <th>Montant</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            echo getSelectedInvoiceServices();
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- end services Section -->
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- <div class="mt-3"> -->
                                        <label for="address" class="form-label">Comment</label>
                                        <textarea name="invoice_comment" id="invoice_comment" class="form-control" rows="5" disabled><?php echo $invoice['comment'] ?></textarea>
                                    <!-- </div> -->
                                </div>
                                <div class="col-md-6 text-end">
                                    <div class="row my-2 ">
                                        <div class="col-sm-6">
                                            <label class="fw-bold">Total partiel:</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="labelSubTotal">0.00 DH</label>
                                        </div>
                                    </div>
                                    <div class="row my-2">
                                        <div class="col-sm-6">
                                            <label class="fw-bold">Remise:</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="labelDiscount">0.00 DH</label>
                                        </div>
                                    </div>
                                    <div class="row my-2">
                                        <div class="col-sm-6">
                                            <label class="fw-bold">TVA:</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="labelTva">0.00 DH</label>
                                        </div>
                                    </div>
                                    <div class="row my-2">
                                        <div class="col-sm-6">
                                            <label class="form-check-label fw-light" for="tvaCheckbox">Enlever TVA&nbsp</label>
                                            <input type="checkbox" class="form-check-input removeTva invoiceRmTVA" name="" id="tvaCheckbox" <?= $invoice['remove_tva']=='1'?'checked':"";?> disabled>
                                        </div>
                                    </div>
                                    <div class="row my-2">
                                        <div class="col-sm-6">
                                            <label class="fw-bold">Total:</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="labelDevisTotal">0.00 DH</label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="row">
            <div class="col-md-12 my-3">
                <input type="submit" name="submit" value="Create Devis" class="btn btn-success float-end">
            </div>
        </div> -->
    </form>
</section>
<div class="row">
    <div class="col-md-12 my-1">
        <a href='invoice-list.php' class="btn btn-secondary float-start"><i class="bi bi-caret-left"></i> Retour</a>
    </div>
</div>
<?php include 'footer.php'; ?>
