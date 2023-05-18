<?php
include 'header.php';



$devis = getSelectedDevisInfo();
$alert;
if($devis['status']==strtolower('rejeter')){
    $alert = 'alert-danger';
}elseif($devis['status']==strtolower('accepter')){
    $alert = 'alert-success';
}else{$alert = 'alert-warning';}

    $checkBroker_devis = checkBroker_devis($devis['id']);
    // if devis has broker 
    if(getBroker_devisData($devis['id']) !=NULL){
        $broker_id = getBroker_devisData($devis['id'])['id_broker'];
        $brokerRow = getBrokerById($broker_id);
        $broker_fullName = ucfirst($brokerRow['prenom']) . ' ' . strtoupper($brokerRow['nom']);
    }
   
    


function br2nl($string)
{
    return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
}
?>

<div class="row my-3">
    <div class="pagetitle col-md-8">
        <h1>View devis</h1>
    </div>
    
    
    <div class="col-md-4 <?= ($role->hasPerm('export devis')) ? "":"hide-element" ?> <?= (strtoupper($devis['type'])==strtoupper("Approved"))? '' :  "hide-element";?>">
        <a target="_blank" href='devis_export.php?id=<?=$_GET['id']?>&client_id=<?=$_GET['client_id']?>' class="btn btn-primary float-end" title="Imprimer Devis"><i class="bi bi-download"></i> Export</a>
        <button class="btn btn-secondary float-end me-2 btnConvertToFacture">Convertir en Facture</button>
    </div>
</div>
<div class="row">
    <div class="alert <?php echo $alert;?>" role="alert">
        <?php echo $devis['status']; ?>
    </div>
</div>

<section class="section">
    <form action="" id="devisViewForm" method="POST">
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
                                <h2 class="h2 fw-bold text-dark float-end ">DEVIS</h2>
                            </div>
                        </div>
                        <div class="col-md-12 my-3">
                            <div class="h-line"></div>
                        </div>
                        <!-- devis content -->
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
                                        <input type="hidden" name="devis_id" id="devis_id" value="<?=$devis['id']?>">
                                        <input type="hidden" id="client_id" name="client_id" >
                                        <input type="text" class="form-control-plaintext fw-bold fs-6 py-0" value="<?php echo getSelectedClientName(); ?>" name="" id="receiverName" disabled>
                                    </div>
                                    <!-- <div class="input-group">
                                        <textarea  class="form-control-plaintext fs-6 py-0 " value="" name="" id="receiverAdr" style='resize: none;' disabled><?php 
                                            $adr_ice = explode('/',getSelectedClientAdr());
                                            if(count($adr_ice) > 1){
                                                $string = $adr_ice[0] . '<br>' . $adr_ice[1];
                                                echo br2nl($string); 
                                            }else{
                                                echo getSelectedClientAdr();
                                            }
                                        
                                        ?></textarea>
                                    </div> -->

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
                                    <label for="" class="col-sm-8 col-form-label fw-bold">N° de devis</label>
                                    <div class="col-sm-4 my-auto">
                                        <input type="text" class="form-control border-dashed p-0" id="devis_number" name="devis_number" value="<?php echo $devis['number'];?>" disabled>
                                    </div>
                                </div>
                                <div class="row my-1">
                                    <label for="" class="col-sm-5 col-form-label fw-bold">Date</label>
                                    <div class="col-sm-7 my-auto">
                                        <input type="date" class="form-control-plaintext " id="" value="<?php echo date('Y-m-d',strtotime($devis['date_creation']));?>" disabled>
                                        
                                        
                                    </div>
                                </div>
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

                            <div class="row my-1">
                                <label for="" class="col-sm-2 col-form-label fw-bold">Objet</label>
                                <div class="col-sm-10 my-auto">
                                    <textarea name="" id="objet_name" class="form-control border-dashed" rows="1" placeholder="Objet" required disabled><?= $devis['objet']; ?></textarea>
                                </div>
                            </div>
                            <div class="row my-1">
                                <label for="" class="col-sm-2 col-form-label fw-bold">Sis à</label>
                                <div class="col-sm-10 my-auto">
                                    <textarea name="" id="sisTxt" class="form-control border-dashed" rows="1" placeholder="Sis à" required disabled><?= $devis['located']; ?></textarea>
                                </div>
                            </div>
                            <!-- end client section -->
                            <!-- services Section -->
                            <div class="col-md-12 my-3">
                                <div class="h-line"></div>
                            </div>
                            <div class="row my-3">
                                <table class="table table-bordered table-hover table-striped servicesTable " id="devisShowTable">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th width="500">Service</th>
                                            <th>U</th>
                                            <th>Qte</th>
                                            <th>Prix</th>
                                            <th>Discount</th>
                                            <th>Montant</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            echo viewDevisServices();
                                        ?>

                                    </tbody>
                                </table>
                                <!-- <div class="col-md-12">
                                    <button class="btn btn-outline-secondary float-start py-1" id="addServiceRowBtn"><i class="bi bi-plus-circle"></i> Ajouter une ligne</button>
                                </div> -->
                            </div>
                            <!-- end services Section -->
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- <div class="mt-3"> -->
                                        <label for="address" class="form-label">Comment</label>
                                        <textarea name="devis_comment" id="devis_comment" class="form-control" rows="5" disabled><?php echo $devis['comment'] ?></textarea>
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
                                            <input type="checkbox" class="form-check-input removeTva" name="" id="tvaCheckbox" <?= $devis['remove_tva']=='1'?'checked':"";?> disabled>
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
                <!-- Broker Devis -->
                <?php  if($checkBroker_devis != 0){ ?>
                    <div class="card" style="background-color: #e9c7c780;">
                        <div class="card-body">
                            <div class="row mt-3 justify-content-between">
                                <div class="col-md-3">
                                    <div class="invoice-logo">
                                        <img src="images/BeplanLogo.png" class="img-fluid" alt="Company Logo">
                                    </div>
                                </div>
                                <!-- <div class="col-md-6"></div> -->
                                <div class="col-md-3">
                                    <h5 class="h5 fw-bold text-secondary text-center">Intermédiaire:</h5>
                                    <h4 class="h4 fw-bold text-dark text-center"><?=$broker_fullName?></h4>
                                </div>
                                <div class="col-md-3">
                                    <h2 class="h2 fw-bold text-dark float-end ">DEVIS</h2>
                                </div>
                            </div>
                            <div class="col-md-12 my-3">
                                <div class="h-line"></div>
                            </div>
                            <!-- devis content -->
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
                                            <input type="hidden" name="devis_id" id="devis_id" value="<?=$devis['id']?>">
                                            <input type="hidden" id="client_id" name="client_id" >
                                            <input type="text" class="form-control-plaintext fw-bold fs-6 py-0" value="<?php echo getSelectedClientName(); ?>" name="" id="receiverName" disabled>
                                        </div>
                                        <!-- <div class="input-group">
                                            <textarea  class="form-control-plaintext fs-6 py-0 " value="" name="" id="receiverAdr" style='resize: none;' disabled><?php 
                                                $adr_ice = explode('/',getSelectedClientAdr());
                                                if(count($adr_ice) > 1){
                                                    $string = $adr_ice[0] . '<br>' . $adr_ice[1];
                                                    echo br2nl($string); 
                                                }else{
                                                    echo getSelectedClientAdr();
                                                }
                                            
                                            ?></textarea>
                                        </div> -->

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
                                        <label for="" class="col-sm-8 col-form-label fw-bold">N° de devis</label>
                                        <div class="col-sm-4 my-auto">
                                            <input type="text" class="form-control border-dashed p-0" id="devis_number" name="devis_number" value="<?php echo $devis['number'];?>" disabled>
                                        </div>
                                    </div>
                                    <div class="row my-1">
                                        <label for="" class="col-sm-5 col-form-label fw-bold">Date</label>
                                        <div class="col-sm-7 my-auto">
                                            <input type="date" class="form-control-plaintext " id="" value="<?php echo date('Y-m-d',strtotime($devis['date_creation']));?>" disabled>
                                            
                                            
                                        </div>
                                    </div>
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

                                <div class="row my-1">
                                    <label for="" class="col-sm-2 col-form-label fw-bold">Objet</label>
                                    <div class="col-sm-10 my-auto">
                                        <textarea name="" id="objet_name" class="form-control border-dashed" rows="1" placeholder="Objet" required disabled><?= $devis['objet']; ?></textarea>
                                    </div>
                                </div>
                                <div class="row my-1">
                                    <label for="" class="col-sm-2 col-form-label fw-bold">Sis à</label>
                                    <div class="col-sm-10 my-auto">
                                        <textarea name="" id="sisTxt" class="form-control border-dashed" rows="1" placeholder="Sis à" required disabled><?= $devis['located']; ?></textarea>
                                    </div>
                                </div>
                                <!-- end client section -->
                                <!-- services Section -->
                                <div class="col-md-12 my-3">
                                    <div class="h-line"></div>
                                </div>
                                <div class="row my-3">
                                    <table class="table table-bordered table-hover table-striped brkServicesTable " id="devisShowTable">
                                        <thead>
                                            <tr style="background-color: #edededed;">
                                                <th></th>
                                                <th width="500">Service</th>
                                                <th>U</th>
                                                <th>Qte</th>
                                                <th>Prix</th>
                                                <th>Discount</th>
                                                <th>Montant</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                echo viewBrokerDevisServices();
                                            ?>

                                        </tbody>
                                    </table>
                                    <!-- <div class="col-md-12">
                                        <button class="btn btn-outline-secondary float-start py-1" id="addServiceRowBtn"><i class="bi bi-plus-circle"></i> Ajouter une ligne</button>
                                    </div> -->
                                </div>
                                <!-- end services Section -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- <div class="mt-3"> -->
                                            <label for="address" class="form-label">Comment</label>
                                            <textarea name="devis_comment" id="devis_comment" class="form-control" rows="5" disabled><?php echo $devis['comment'] ?></textarea>
                                        <!-- </div> -->
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <div class="row my-2 ">
                                            <div class="col-sm-6">
                                                <label class="fw-bold">Total partiel:</label>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="labelBrkSubTotal">0.00 DH</label>
                                            </div>
                                        </div>
                                        <div class="row my-2">
                                            <div class="col-sm-6">
                                                <label class="fw-bold">Remise:</label>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="labelBrkDiscount">0.00 DH</label>
                                            </div>
                                        </div>
                                        <div class="row my-2">
                                            <div class="col-sm-6">
                                                <label class="fw-bold">TVA:</label>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="labelBrkTva">0.00 DH</label>
                                            </div>
                                        </div>
                                        <div class="row my-2">
                                            <div class="col-sm-6">
                                                <label class="form-check-label fw-light" for="tvaCheckbox">Enlever TVA&nbsp</label>
                                                <input type="checkbox" class="form-check-input removeTva" name="" id="tvaCheckbox" <?= $devis['remove_tva']=='1'?'checked':"";?> disabled>
                                            </div>

                                        </div>
                                        <div class="row my-2">
                                            <div class="col-sm-6">
                                                <label class="fw-bold">Total:</label>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="labelBrkDevisTotal">0.00 DH</label>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }?>
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
        <a href='devis-view.php' class="btn btn-secondary float-start"><i class="bi bi-caret-left"></i> Retour</a>
    </div>
</div>
<?php include 'footer.php'; ?>
