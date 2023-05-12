<?php
include 'header.php';
?>

<form action="" id="invoiceForm" method="POST">
    <div class="row my-2">
        <div class="pagetitle col-md-8">
            <h1>Creer une facture</h1>
        </div>
        <!-- <div class="col-md-4">
            <button class="btn btn-outline-dark float-end btn-invoice-payment" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                <i class="bi bi-bank"></i>&nbsp;Entrer un paiement
            </button>
            <div class="collapse payment-container" id="collapseExample">
                <div class="card">
                    <div class="card-header p-2">
                        <button type="button" class="btn-close float-end btn-close-inv-pay" aria-label="Close"></button>
                    </div>
                    <div class="card-body">
                        <div class="row my-2">
                            <div class="col-md-6 my-1 p-0">
                                <input type="number" min="0"  step="0.01" class="form-control py-1 invoice-prix-txt" name="" id="invoice_payment" placeholder="prix">
                            </div>
                            <div class="col-md-6 my-1">
                                <button class="btn btn-success btn-sm float-end btn-save-payment"><i class="bi bi-check-lg"></i>&nbsp;Enregistrer</button>
                            </div>
                        </div>
                        <div class="row my-2">
                            <div class="col-md-12 p-0">
                                <input type="text" class="form-control py-1 invoice-giver-txt" name="" id="invoice_payment_giver" placeholder="Le Nom du Donateur">
                            </div>
                        </div>
                        <div class="row">
                            <select name="" class="form-select py-1 select-pay-method" id="pay_method">
                                <option value="" selected disabled>Choisissez un mode de paiement</option>
                                <option value="Espéce">Espéce</option>
                                <option value="Check">Check</option>
                                <option value="Virement">Virement</option>
                                <option value="Trita">Trita</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

    </div>

    <section class="section">
        
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
                                <h2 class="h2 fw-bold text-dark float-end ">FACTURE</h2>
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
                            <div class="col-md-4">
                                <label class="fw-bold">À</label>
                                <fieldset class="border-dashed">
                                    <div class="input-group">
                                        <input type="hidden" id="client_id" name="client_id">
                                        <input type="hidden" id="client_type" name="client_type">
                                        <input type="text" class="form-control-plaintext fw-bold fs-6 py-0" placeholder="Client" name="" id="receiverName" disabled>
                                    </div>
                                    <div class="input-group">
                                        <input type="text" class="form-control-plaintext fs-6 py-0 " placeholder="Address Client" name="" id="receiverAdr" disabled>
                                    </div>
                                    <!-- <div class="input-group">
                                        <input type="text" class="form-control-plaintext fs-6 py-0 " placeholder="Something..." name="" id="">
                                    </div> -->
                                </fieldset>
                                <div class="text-center py-1">
                                    <a href="javascript:void(0)" id="selectClientModal" class="" title="Sélectionner Client"><i class="bi bi-box-arrow-up-right" style="font-size:12px"></i> Select client</a>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="row my-1">
                                    <label for="" class="col-sm-8 col-form-label fw-bold">N° de facture</label>
                                    <div class="col-sm-4 my-auto">
                                        <input type="text" class="form-control border-dashed p-0" id="invoice_number" name="invoice_number" value="<?php echo sprintf("%03d", getInvoiceNumber()) . '/' . date('Y'); ?>" disabled>
                                    </div>
                                </div>
                                <div class="row my-1">
                                    <label for="" class="col-sm-5 col-form-label fw-bold">Date</label>
                                    <div class="col-sm-7 my-auto">
                                        <input type="date" class="form-control-plaintext " id="" value="<?= date('Y-m-d') ?>">
                                    </div>
                                </div>
                                <!-- <div class="row my-1">
                                    <label for="" class="col-sm-5 col-form-label fw-bold">Date d'échéance</label>
                                    <div class="col-sm-7 my-auto">
                                        <input type="date" class="form-control-plaintext " id="due_date" value="<?= date('Y-m-d') ?>">
                                    </div>
                                </div> -->
                                
                                <!-- <div class="row my-1">
                                    <label for="" class="col-sm-5 col-form-label fw-bold">Projet</label>
                                    <div class="col-sm-7 my-auto">
                                        <textarea name="" id="" class="form-control border-dashed" rows="1">Projet name...</textarea>
                                    </div>
                                </div> -->
                            </div>
                            <div class="row my-1">
                                <label for="" class="col-sm-2 col-form-label fw-bold">Objet</label>
                                <div class="col-sm-10 my-auto">
                                    <textarea name="" id="objet_name" class="form-control border-dashed" rows="1" placeholder="Objet" required></textarea>
                                </div>
                            </div>
                            <div class="row my-1">
                                <label for="" class="col-sm-2 col-form-label fw-bold">Sis à</label>
                                <div class="col-sm-10 my-auto">
                                    <textarea name="" id="sisTxt" class="form-control border-dashed" rows="1" placeholder="Sis à" required></textarea>
                                </div>
                            </div>
                            <!-- end client section -->
                            <!-- services Section -->
                            <div class="col-md-12 my-3">
                                <div class="h-line"></div>
                            </div>
                            <div class="row my-3 overflow-auto">
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
                                    <textarea name="invoice_comment" id="invoice_comment" class="form-control" rows="5"></textarea>
                                    <!-- </div> -->
                                </div>
                                <div class="col-md-6 text-end">
                                    <div class="row my-2 ">
                                        <div class="col-sm-6">
                                            <label class="fw-bold">Sub Total:</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="labelSubTotal" id="labelSubTotal">0.00 DH</label>
                                        </div>
                                    </div>
                                    <div class="row my-2">
                                        <div class="col-sm-6">
                                            <label class="fw-bold">Discount:</label>
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
                                            <label class="form-check-label fw-light" for="tvaCheckbox">Remove TVA&nbsp</label>
                                            <input type="checkbox" class="form-check-input removeTva" name="" id="tvaCheckbox">
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
                                    <!-- <div class="row my-2">
                                        <div class="col-sm-6">
                                            <label class="fw-bold">Paiement(s):</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="labelInvoicePayment" id="labelInvoicePayment">0.00 DH</label>
                                        </div>
                                    </div> -->
                                    <!-- <div class="row my-2">
                                        <div class="col-sm-6">
                                            <label class="fw-bold">Solde à payer:</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="labelInvoiceSolde" id="labelInvoiceSolde">0.00 DH</label>
                                        </div>
                                    </div> -->

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 my-3">
                <input type="submit" name="submit" id="inv_add" value="Create Facture" class="btn btn-success float-end" title="Créer Facture">
            </div>
        </div>
    </section>
</form>


<!-- Modal Clients-->
<div class="modal fade" id="clientShowModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Select An Existing Customer</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <!-- start modal Client-->
                <!-- Tabs navs -->
                <ul class="nav nav-tabs mb-3" id="client" role="tablist">

                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="indiv-tab" data-bs-toggle="tab" data-bs-target="#indiv-tabs" type="button" role="tab" aria-controls="indiv-tabs" aria-selected="true">Individual List</button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link " id="entrep-tab" data-bs-toggle="tab" data-bs-target="#entrep-tabs" type="button" role="tab" aria-controls="entrep-tabs" aria-selected="false">Entreprise List</button>
                    </li>

                </ul>
                <!-- Tabs navs END -->

                <div class="tab-content mt-4" id="client-content" style="overflow:auto;">
                    <div class="tab-pane fade show active" id="indiv-tabs" role="tabpanel" aria-labelledby="indiv-tab">
                        <!-- table Individual -->
                        <div class="">
                            <div>
                                <table id="devisClientIndvTable" class="table table-hover table-bordered table-striped mt-5" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>
                                                <a href="#">N°</a>
                                            </th>
                                            <th>
                                                <a href="#">Prenom</a>
                                            </th>
                                            <th>
                                                <a href="#">Nom</a>
                                            </th>
                                            <th>
                                                <a href="#">Email</a>
                                            </th>
                                            <th>
                                                <a href="#">Phone</a>
                                            </th>
                                            <th>
                                                <a href="#">Address</a>
                                            </th>
                                            <th>
                                                <a href="#">Action</a>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                            $res = getIndvClientData();
                                            $number = 1;
                                            $html = '';
                                            while($row = mysqli_fetch_assoc($res)){
                                                $html .= '<tr>';
                                                $html .= '<td>'.$number.'</td>';
                                                $html .= '<td>'.$row['prenom'].'</td>';
                                                $html .= '<td>'.$row['nom'].'</td>';
                                                $html .= '<td>'.$row['email'].'</td>';
                                                $html .= '<td>'.$row['tel'].'</td>';
                                                $html .= '<td>'.$row['address'].'</td>';
                                                $html .= '  <td>
                                                                <a href="javascript:void(0);" data-id="'.$row['id'].'" class="btn btn-primary btn-sm selectIndvBtn" title="Sélectionner un Client"><span>Select</span></a>
                                                            </td>';
                                                $html .= '</tr>';
                                                $number++;
                                            }
                                            echo $html;
                                                
                                                
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- table Individual END -->
                    </div>
                    <!-- table entreprise -->
                    <div class="tab-pane fade" id="entrep-tabs" role="tabpanel" aria-labelledby="entrep-tab">
                        <div class="">
                            <div>
                                <table id="devisClientEntrepTable" class="table table-hover table-bordered table-striped mt-5" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>
                                                <a href="#">N°</a>
                                            </th>
                                            <th>
                                                <a href="#">Nom</a>
                                            </th>
                                            <th>
                                                <a href="#">ICE</a>
                                            </th>
                                            <th>
                                                <a href="#">Email</a>
                                            </th>
                                            <th>
                                                <a href="#">Phone</a>
                                            </th>
                                            <th>
                                                <a href="#">Address</a>
                                            </th>
                                            <th>
                                                <a href="#">Action</a>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php

                                        $res = getEntrepClientData();
                                        $number = 1;
                                        $html = '';
                                        while($row = mysqli_fetch_assoc($res)){
                                            $html .= '<tr>';
                                            $html .= '<td>'.$number.'</td>';
                                            $html .= '<td>'.$row['nom'].'</td>';
                                            $html .= '<td>'.$row['ICE'].'</td>';
                                            $html .= '<td>'.$row['email'].'</td>';
                                            $html .= '<td>'.$row['tel'].'</td>';
                                            $html .= '<td>'.$row['address'].'</td>';
                                            $html .= '  <td>
                                                            <a href="javascript:void(0);" data-id="'.$row['id'].'" class="btn btn-primary btn-sm selectEntrepBtn" title="Sélectionner un Client"><span>Select</span></a>
                                                        </td>';
                                            $html .= '</tr>';
                                            $number++;
                                        }
                                        echo $html;
                                            
                                            
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- table entreprise end -->
                </div>
                <!-- end Modal Client -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <!-- <button type="submit" name="submit" class="btn btn-primary">Update</button> -->
            </div>

        </div>
    </div>
</div>
<!--  Modal Clients END -->


<?php include 'footer.php'; ?>

<script>
    $(document).ready(function() {
        $(document).on('click', '#addServiceRowBtn', function(e) {
            e.preventDefault();
            html = '';
            html += '<tr>';
            html += '<td><i class="bi bi-trash fs-5 deleteRowBtn" title="Supprimer la ligne"></i></td>';
            html += '<td class="input-group"><input type="text" class="input-group-text w-25 servRefTxt" id="srvRT" placeholder="Reference" autocomplete="off" required data-bs-placement="bottom" data-bs-content="Cette référence existe déjà" data-bs-trigger="manual" data-bs-custom-class="error-popover"><input type="text" id="servicesListId" list="servicesList" autocomplete="off" class="form-control serviceDropdown"  aria-describedby="srvRT" placeholder="Service name"><datalist id="servicesList"><?php echo fill_service_dropDown(); ?></datalist></td>';
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
                $(this).addClass('border-danger');
                } else {
                $(this).removeClass('border-danger');
                }
            });
    });
</script>