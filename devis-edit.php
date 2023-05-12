<?php
include 'header.php';

$devis = getSelectedDevisInfo();

function br2nl($string)
{
    return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
}
?>

<div class="row">
    <div class="pagetitle col-md-8">
        <h1>Modifier Devis</h1>
    </div>
    <div class="col-md-4">
        <select class="form-select float-end my-1" name="" id="devisStatusDropdown">
            <option value="encours" <?php if(strtolower($devis['status'])=='encours'){echo 'selected';} ?>>Encours</option>
            <option value="accepter" <?php if(strtolower($devis['status'])=='accepter'){echo 'selected';} ?> >Accepter</option>
            <option value="rejeter" <?php if(strtolower($devis['status'])=='rejeter'){echo 'selected';} ?> >Rejeter</option>
        </select>
    </div>
    <input type="hidden" name="" id="devis_id" value='<?php echo $devis['id'];?>'>
</div>
<section class="section">
    <form action="devis-view.php" id="devisEditForm" method="POST">
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
                                    <input type="text" name="" class="form-control-plaintext p-0" value="BEPLAN" required placeholder="title" readonly>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label class="fw-bold">À</label>
                                <fieldset class="border-dashed">
                                    <div class="input-group">
                                        <input type="hidden" id="client_id" name="client_id" value="<?php echo $devis['id_client'] ;?>">
                                        <input type="text" class="form-control-plaintext fw-bold fs-6 py-0" value="<?php echo getSelectedClientName(); ?>" name="" id="receiverName" disabled>
                                    </div>
                                    <!-- <div class="input-group">
                                        <input type="text" class="form-control-plaintext fs-6 py-0 " value="<?php echo getSelectedClientAdr(); ?>" name="" id="receiverAdr" disabled>
                                    </div> -->
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
                                    <label for="" class="col-sm-8 col-form-label fw-bold">N° de devis</label>
                                    <div class="col-sm-4 my-auto">
                                        <input type="text" class="form-control border-dashed p-0" id="devis_number" name="devis_number" value="<?php echo $devis['number'];?>" disabled>
                                    </div>
                                </div>
                                <div class="row my-1">
                                    <label for="" class="col-sm-5 col-form-label fw-bold">Date</label>
                                    <div class="col-sm-7 my-auto">
                                        <input type="date" class="form-control-plaintext " id="" value="<?php echo date('Y-m-d',strtotime($devis['date_creation']));?>">
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
                                <label for="" class="col-sm-2 col-form-label fw-bold">Object</label>
                                <div class="col-sm-10 my-auto">
                                    <textarea name="" id="objet_name" class="form-control border-dashed" rows="1" placeholder="Objet" required><?= $devis['objet'];?></textarea>
                                </div>
                            </div>
                            <div class="row my-1">
                                <label for="" class="col-sm-2 col-form-label fw-bold">Sis à</label>
                                <div class="col-sm-10 my-auto">
                                    <textarea name="sisTxt" id="sisTxt" class="form-control border-dashed" rows="1" placeholder="Sis à" required ><?= $devis['located']; ?></textarea>
                                </div>
                            </div>
                            <!-- end client section -->
                            <!-- services Section -->
                            <div class="col-md-12 my-3">
                                <div class="h-line"></div>
                            </div>
                            <div class="row my-3">
                                <table class="table table-bordered table-hover table-striped servicesTable">
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
                                            echo getSelectedDevisServices();
                                        ?>

                                    </tbody>
                                </table>
                                <div class="col-md-12">
                                    <button class="btn btn-outline-secondary float-start py-1" id="addServiceRowBtn" title="Ajouter une nouvelle ligne"><i class="bi bi-plus-circle"></i> Ajouter une ligne</button>
                                </div>
                            </div>
                            <!-- end services Section -->
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- <div class="mt-3"> -->
                                        <label for="address" class="form-label">Comment</label>
                                        <textarea name="devis_comment" id="devis_comment" class="form-control" rows="5"><?php echo $devis['comment'] ?></textarea>
                                    <!-- </div> -->
                                </div>
                                <div class="col-md-6 text-end">
                                    <div class="row my-2 ">
                                        <div class="col-sm-6">
                                            <label class="fw-bold">Total partiel:</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="labelSubTotal" id="labelSubTotal">0.00 DH</label>
                                        </div>
                                    </div>
                                    <div class="row my-2">
                                        <div class="col-sm-6">
                                            <label class="fw-bold">Remise:</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="labelDiscount" id="labelDiscount">0.00 DH</label>
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
                                            <input type="checkbox" class="form-check-input removeTva" name="" id="tvaCheckbox" <?= $devis['remove_tva']=='1'?'checked':"";?> >
                                        </div>

                                    </div>
                                    <div class="row my-2">
                                        <div class="col-sm-6">
                                            <label class="fw-bold">Total:</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="labelDevisTotal" id="labelDevisTotal">0.00 DH</label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 my-3">
                <input type="submit" name="submit" id="dev_upd" value="Update Devis" class="btn btn-success float-end" title="Modifier Devis" >
            </div>
        </div>
    </form>
</section>

<?php include 'footer.php'; ?>
<script>
    $(document).ready(function() {
        $(document).on('click', '#addServiceRowBtn', function(e) {
            e.preventDefault();
            html = '';
            html += '<tr>';
            html += '<td><i class="bi bi-trash fs-5 deleteRowBtn" title="Supprimer la ligne"></i></td>';
            html += '<td class="input-group"><input type="text" class="input-group-text w-25 servRefTxt" id="srvRT" placeholder="Reference" autocomplete="off" required data-bs-placement="bottom" data-bs-content="Cette référence existe déjà" data-bs-trigger="manual" data-bs-custom-class="error-popover"><input type="text" id="servicesListId" list="servicesList" autocomplete="off" class="form-control serviceDropdown" aria-describedby="srvRT" placeholder="Service name"><datalist id="servicesList"><?php echo fill_service_dropDown(); ?></datalist></td>';
            html += '<td><input type="text" name="" class="form-control py-1 serviceUnit"  placeholder="Unité" required></td>';
            html += '<td><input type="number" min="0" name="" class="form-control py-1 px-1 rowServiceQte" value="1" placeholder="Quantité"></td>';
            html += '<td><input type="number" min="0"  step="0.01" name="" class="form-control py-1 px-1 servicePrice" placeholder="0.00"></td>';
            html += '<td><div class="input-group"><span class="input-group-text py-1"><i class="bi bi-percent"></i></span><input type="number" min="0" max="100" name="" class="form-control py-1 serviceDiscount" placeholder="Enter % (ex: 10%)"></div></td>';
            html += '<td><input type="text" name="" class="form-control py-1 rowServiceTotal" disabled placeholder="0"></td>';
            html += '</tr>';
            $('.servicesTable tbody').append(html);
        });
        $(document).on('input', '.serviceDiscount',function() {
                var value = parseInt($(this).val(), 10);
                if (value < 0 || value > 100) {
                // $(this).addClass('border-danger');
                $(this).parent().find('.input-group-text').addClass('bg-danger text-white' );
                } else {
                // $(this).removeClass('border-danger');
                $(this).parent().find('.input-group-text').removeClass('bg-danger text-white');
                }
            });
    });
</script>