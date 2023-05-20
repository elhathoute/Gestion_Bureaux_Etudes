<?php
include 'header.php';

?>

<div class="row my-3 align-items-center">
    <div class="pagetitle col-md-6">
        <h1>Ajouter un devis</h1>
    </div>
    <div class="col-md-6">
        <!-- <div>
            <button class="btn btn-outline-dark float-end btn-invoice-payment ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
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
        <a href="javascript:void(0);" id="devisAddBrkBtn_1" class="btn btn-outline-dark float-end devisAddBrkBtn"><i class="bi bi-person-lines-fill"></i>&nbsp; Ajouter intermédiaire</a>
    </div>
</div>
<section class="section">
    <form action="" id="devisForm" method="POST">
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
                                    <input type="text" name="" class="form-control-plaintext p-0 " value="BEPLAN" required placeholder="title" disabled>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label class="fw-bold">À</label>
                                <fieldset class="border-dashed">
                                    <div class="input-group">
                                        <input type="hidden" id="client_id" name="client_id">
                                        <input type="hidden" id="client_type" name="client_type">
                                        <input type="hidden" id="selectedBrkId" name="selectedBrkId">
                                        <input type="text" class="form-control-plaintext fw-bold fs-6 py-0" placeholder="Client" name="" id="receiverName" disabled>
                                    </div>
                                    <!-- <div class="input-group">
                                        <textarea class="form-control-plaintext fs-6 py-0  hidden" placeholder="Client Address" name="" id="receiverAdr" style='resize: none' disabled></textarea>
                                    </div> -->
                                    <!-- <div class="input-group">
                                        <input type="text" class="form-control-plaintext fs-6 py-0 " placeholder="Something..." name="" id="">
                                    </div> -->
                                </fieldset>
                                <div class="text-center py-1">
                                    <a href="javascript:void(0)" id="selectClientModal" class="" title="Sélectionner Client"><i class="bi bi-box-arrow-up-right" style="font-size:12px"></i> Select client</a>
                                </div>
                            <!-- show broker (intermidiare) -->

                                <div class="text-center py-1">
                               <div class="d-flex align-items-center justify-content-center">

                                <fieldset class="border-dashed">
                                <input type="text"  class="form-control-plaintext fw-bold fs-6 py-0" placeholder="Intermédiaire" name="selectedBrkName" id="selectedBrkName" disabled>
                               
                                </fieldset>
                               <a href="#" title="Annuler Intermédiaire" id="removeBkr" class="text-danger d-none mx-2"><i class="bi bi-person-x"></i></a>
                               </div>
                            <div class="text-center py-1">
                            <a href="javascript:void(0)" id="devisAddBrkBtn_2" class="devisAddBrkBtn" title="Sélectionner Intermédiaire"><i class="bi bi-box-arrow-up-right" style="font-size:12px"></i> Select Intermédiaire</a>

                            </div>

                                </div>
                            </div>
                        
                            <div class="col-md-4">
                                <div class="row my-1">
                                    <label for="" class="col-sm-8 col-form-label fw-bold">N° de devis</label>
                                    <div class="col-sm-4 my-auto">
                                        <!-- <input type="text" class="form-control border-dashed p-0" id="devis_number" name="devis_number" value="<?php echo sprintf("%03d", getDevisNumber()) . '/' . date('Y'); ?>" disabled> -->
                                        <input type="text" class="form-control border-dashed p-0" id="devis_number" name="devis_number" value="<?= '/' . date('Y'); ?>" >
                                    </div>
                                </div>
                                <div class="row my-1">
                                    <label for="" class="col-sm-5 col-form-label fw-bold">Date</label>
                                    <div class="col-sm-7 my-auto">
                                        <input type="date" class="form-control-plaintext " id="dateTxt" value="<?= date('Y-m-d') ?>">
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
                                <table class="table table-bordered table-hover table-striped servicesTable" id="devisSrvTbl">
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
                                        <!-- <tr>
                                            <td>
                                                <i class="bi bi-trash fs-5 deleteRowBtn"></i>
                                            </td>
                                            <td>
                                                <input type="text" list="az" class="form-control serviceDropdown">
                                                <datalist id="az">
                                                    <?php echo fill_service_dropDown(); ?>
                                                </datalist>
                                            </td>
                                            <td>
                                                <input type="number" min="0" name="" class="form-control py-1 px-1" placeholder="Quantité">
                                            </td>
                                            <td>
                                                <input type="number" min="0"  step="0.01" name=""  class="form-control py-1 px-1 servicePrice" placeholder="0.00">
                                            </td>
                                            <td>
                                                <input type="number" min="0" name="" class="form-control py-1" placeholder="Enter % (ex: 10%)">
                                            </td>
                                            <td>
                                                <input type="text" name="" class="form-control py-1" disabled placeholder="0">
                                            </td>
                                            
                                            
                                        </tr> -->

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
                                    <textarea name="devis_comment" id="devis_comment" class="form-control" rows="5"></textarea>
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
                                            <label class="labelTva" id="labelTva">0.00 DH</label>
                                        </div>
                                    </div>
                                    <div class="row my-2">
                                        <div class="col-sm-6">
                                            <label class="form-check-label fw-light" for="tvaCheckbox">Enlever TVA&nbsp</label>
                                            <input type="checkbox" class="form-check-input removeTva removeTvaClient" name="" id="tvaCheckbox">
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
                <input type="submit" name="submit" id="dev_add" value="Create Devis" class="btn btn-success float-end" title="Créer Devis">
            </div>
        </div>
    </form>
</section>


<!-- Modal Clients-->
<div class="modal fade" id="clientShowModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel"><strong>Sélectionner un client existant</strong></h1>
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
                                                <a href="#">Téléphone</a>
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
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            $html .= '<tr>';
                                            $html .= '<td>' . $number . '</td>';
                                            $html .= '<td>' . $row['prenom'] . '</td>';
                                            $html .= '<td>' . $row['nom'] . '</td>';
                                            $html .= '<td>' . $row['email'] . '</td>';
                                            $html .= '<td>' . $row['tel'] . '</td>';
                                            $html .= '<td>' . $row['address'] . '</td>';
                                            $html .= '  <td>
                                                                <a href="javascript:void(0);" data-id="' . $row['id'] . '" class="btn btn-primary btn-sm selectIndvBtn" title="Sélectionner un Client"><span>Select</span></a>
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
                                                <a href="#">Téléphone</a>
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
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            $html .= '<tr>';
                                            $html .= '<td>' . $number . '</td>';
                                            $html .= '<td>' . $row['nom'] . '</td>';
                                            $html .= '<td>' . $row['ICE'] . '</td>';
                                            $html .= '<td>' . $row['email'] . '</td>';
                                            $html .= '<td>' . $row['tel'] . '</td>';
                                            $html .= '<td>' . $row['address'] . '</td>';
                                            $html .= '  <td>
                                                            <a href="javascript:void(0);" data-id="' . $row['id'] . '" class="btn btn-primary btn-sm selectEntrepBtn" title="Sélectionner un Client"><span>Select</span></a>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <!-- <button type="submit" name="submit" class="btn btn-primary">Update</button> -->
            </div>

        </div>
    </div>
</div>
<!--  Modal Clients END -->

<!-- Modal Broker -->

<div class="modal fade" id="devisBrokerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-semibold" id="staticBackdropLabel">Sélectionner intermédiaire</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="javascript:void(0)" id="devisBrokerForm" method="POST">

                <div class="modal-body">
                    <!-- table Individual -->
                    <div class="overflow-auto">
                        <div>
                            <table id="devisBrkTable" class="table table-hover table-bordered table-striped mt-5" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>
                                            <a href="#">N°</a>
                                        </th>
                                        <th>
                                            <a href="#">Intermédiaire</a>
                                        </th>

                                        <th>
                                            <a href="#">Action</a>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $brokerData = getBrokerData();
                                    $brkNumber = 1;
                                    $html = '';
                                    while ($row = mysqli_fetch_assoc($brokerData)) {
                                        $html .= '<tr>';
                                        $html .= '<td>' . $brkNumber . '</td>';
                                        $html .= '<td>' . ucfirst($row["nom"]) . ' ' . ucfirst($row["prenom"]) . '</td>';
                                        $html .= '  <td>
                                                                <a href="javascript:void(0);" data-nom="'.$row['nom'].' '.$row['prenom'].'" data-id="' . $row['id'] . '" class="btn btn-primary btn-sm selectDevisBrkBtn" title="Sélectionner un intermédiaire"><span>Select</span></a>
                                                            </td>';
                                        $html .= '</tr>';
                                        $brkNumber++;
                                    }
                                    echo $html;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- table Individual END -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Modal Broker End -->

<!-- Modal Broker Devis -->

<div class="modal fade" id="devisBrokerViewModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-semibold" id="staticBackdropLabel">Devis de Intermédiaire </h1>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>

            <div class="modal-body">
                <!-- here goes the body -->
                <!-- devis broker Start -->

                <section class="section">
                    <form action="" id="devisBrokerViewForm" method="POST">
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
                                                        
                                                        <input type="text" class="form-control-plaintext fw-bold fs-6 py-0"  name="" id="brkReceiverName" value="" disabled>
                                                    </div>
                                                    <!-- <div class="input-group">
                                                        <textarea class="form-control-plaintext fs-6 py-0 " value="" name="" id="brkReceiverAdr" style='resize: none;' disabled>
                                                        
                                                        </textarea>
                                                    </div> -->
                                                   
                                            </div>
                                            <div class="col-md-4">
                                                <div class="row my-1">
                                                    <label for="" class="col-sm-8 col-form-label fw-bold">N° de devis</label>
                                                    <div class="col-sm-4 my-auto">
                                                        <input type="text" class="form-control border-dashed p-0" id="brkDevis_number" name="devis_number" value="" disabled>
                                                    </div>
                                                </div>
                                                <div class="row my-1">
                                                    <label for="" class="col-sm-5 col-form-label fw-bold">Date</label>
                                                    <div class="col-sm-7 my-auto">
                                                        <input type="date" class="form-control-plaintext " id="brk_dateTxt" value="" disabled>


                                                    </div>
                                                </div>
                                                
                                            </div>

                                            <div class="row my-1">
                                                <label for="" class="col-sm-2 col-form-label fw-bold">Objet</label>
                                                <div class="col-sm-10 my-auto">
                                                    <textarea name="" id="BrkObjet_name" class="form-control border-dashed" rows="1" placeholder="Objet" required disabled></textarea>
                                                </div>
                                            </div>
                                            <div class="row my-1">
                                                <label for="" class="col-sm-2 col-form-label fw-bold">Sis à</label>
                                                <div class="col-sm-10 my-auto">
                                                    <textarea name="" id="brkSisTxt" class="form-control border-dashed" rows="1" placeholder="Sis à" required disabled></textarea>
                                                </div>
                                            </div>
                                            <!-- end client section -->
                                            <!-- services Section -->
                                            <div class="col-md-12 my-3">
                                                <div class="h-line"></div>
                                            </div>
                                            <div class="row my-3">
                                                <table class="table table-bordered table-hover table-striped  devisShowTableBrk" id="devisBrkShowTable">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th width="500">Service</th>
                                                            <th>U</th>
                                                            <th>Qte</th>
                                                            <th>Prix</th>
                                                            <th>Discount</th>
                                                            <th>Montant</th>
                                                             <th>Uniquee_Srv_id</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        
                                                    </tbody>
                                                </table>
                                                
                                            </div>
                                            <!-- end services Section -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <!-- <div class="mt-3"> -->
                                                    <label for="brkDevis_comment" class="form-label">Comment</label>
                                                    <textarea name="devis_comment" id="brkDevis_comment" class="form-control" rows="5" disabled></textarea>
                                                    <!-- </div> -->
                                                </div>
                                                <div class="col-md-6 text-end">
                                                    <div class="row my-2 ">
                                                        <div class="col-sm-6">
                                                            <label class="fw-bold">Total partiel:</label>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="labelSubTotal labelBrkSubTotal">0.00 DH</label>
                                                        </div>
                                                    </div>
                                                    <div class="row my-2">
                                                        <div class="col-sm-6">
                                                            <label class="fw-bold">Remise:</label>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="labelDiscount labelBrkDiscount">0.00 DH</label>
                                                        </div>
                                                    </div>
                                                    <div class="row my-2">
                                                        <div class="col-sm-6">
                                                            <label class="fw-bold">TVA:</label>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="labelTva labelBrkTva">0.00 DH</label>
                                                        </div>
                                                    </div>
                                                    <div class="row my-2">
                                                        <div class="col-sm-6">
                                                            <label class="form-check-label fw-light" for="adrtextcb">Enlever TVA&nbsp</label>
                                                            <input type="checkbox" class="form-check-input removeTva removeTva_broker" name="" id="BrkTvaCheckbox"  disabled>
                                                        </div>

                                                    </div>
                                                    <div class="row my-2">
                                                        <div class="col-sm-6">
                                                            <label class="fw-bold">Total:</label>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="labelDevisTotal labelBrkDevisTotal">0.00 DH</label>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </section>

                <!-- devis broker End -->

            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                <button type="button" class="btn btn-success btn_brk_devis_confirm" data-bs-dismiss="modal">Continuer</button>
            </div>

        </div>
    </div>
</div>

<!-- Modal Broker Devis End -->


<?php include 'footer.php'; ?>
<script>
    $(document).ready(function() {
        $(document).on('click', '#addServiceRowBtn', function(e) {
            e.preventDefault();
            html = '';
            html += '<tr>';
            html += '<td><i class="bi bi-trash fs-5 deleteRowBtn" title="Supprimer la ligne"></i></td>';
            html += '<td class="input-group"><input type="text" class="input-group-text w-25 servRefTxt" id="srvRT" placeholder="Reference" autocomplete="off" required data-bs-placement="bottom" data-bs-content="Cette référence existe déjà" data-bs-trigger="manual" data-bs-custom-class="error-popover"><input type="text" id="servicesListId" list="servicesList" autocomplete="off" class="form-control serviceDropdown" aria-describedby="srvRT" placeholder="Service name"><datalist id="servicesList"><?php echo fill_service_dropDown(); ?></datalist></td>';
            html += '<td><input type="text" name="" class="form-control py-1 serviceUnit" placeholder="Unité" required></td>';
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