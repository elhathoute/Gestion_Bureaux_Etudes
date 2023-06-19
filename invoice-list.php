<?php
include 'header.php';


if(isset($_GET["sc"])){
    $msg='';
    if($_GET["sc"]=="sucadd"){
        $msg = '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i>&nbsp;
        <strong>Facture Added Successfully.</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    }elseif ($_GET["sc"]=="sucupd") {
        $msg = '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i>&nbsp;
        <strong>Facture Updated Successfully.</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    }
    
    $_GET['sc'] = $msg;
    echo $_GET['sc'];
    unset($_GET['sc']);
}


?>

<div class="pagetitle">
    <h1>Lises de Factures</h1>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Information des factures</h3>
                    <div class="tab-content" id="services-content">
                        <!-- table Invoice -->
                        <div class="overflow-auto">
                            <div>
                                <table id="invoiceTable" class="table table-hover table-bordered table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>
                                                <a href="#">N°</a>
                                            </th>
                                            <th>
                                                <a href="#">N°Facture</a>
                                            </th>
                                            <th>
                                                <a href="#">Maître d'ouvrage</a>
                                            </th>
                                            <th width="130">
                                                <a href="#">Objet</a>
                                            </th>
                                            <th>
                                                <a href="#">Date creation</a>
                                            </th>
                                            <th>
                                                <a href="#">Net Total</a>
                                            </th>
                                            <th>
                                                <a href="#">Solde</a>
                                            </th>
                                            <th>
                                                <a href="#">status</a>
                                            </th>
                                            <th>
                                                <a href="#">Action</a>
                                            </th>
                                            
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $query = "CALL `sp_getInvoice`();";
                                            $res = mysqli_query($cnx,$query);
                                            $number = 1;
                                            //check for permissions
                                            $check_edit =  ($role->hasPerm('edit invoice')) ? "":"hide-element";
                                            $check_delete = ($role->hasPerm('delete invoice')) ? "":"hide-element";
                                            while($row=mysqli_fetch_assoc($res)){
                                                // $check_client = ($row['status']=="accepter" && strtolower($row['type'])=="approved")? '<span><i class="bi bi-check-circle btn btn-outline-success btn-sm rounded-circle btn-client-approve" data-id="'.$row['id'].'" ></i></span>' :'';
                                                $broker_id='';
                                                if($row['broker_id']!=0){
                                                    $broker_id='&broker_id='.$row['broker_id'].'';
                                                }
                                                $paid_invoice = ($row['paid_inv']=="1")? "approved_row" :"";
                                                $solde = (floatval($row["net_total"]) - floatval($row['solde']))<0 ? 0 : (floatval($row["net_total"]) - floatval($row['solde']));
                                                echo '<tr class="'.$paid_invoice.'" >
                                                    <td>'.$number.'</td>
                                                    <td>'.$row["F_number"].'</td>
                                                    <td>'.$row["client"].'</td>
                                                    <td>'.$row["objet"].'</td>
                                                    <td >'.$row["date_creation"].'</td>
                                                    <td >'.$row["net_total"].' DH</td>
                                                    <td >'.number_format($solde,2).' DH</td>
                                                    <td><span class="'.styleStatus($row["status"]).'">'.$row["status"].'</span></td>
                                                    <td>
                                                        <a href="invoice-view.php?id='.$row['id'].'&client_id='.$row['client_id'].''.$broker_id.'" data-id="'.$row['id'].'" data-id_client="'.$row['client_id'].'" class="btn btn-secondary btn-sm viewInvoiceBtn" title="Afficher Facture" ><span><i class="bi bi-eye"></i></span></a>
                                                        <a href="invoice-edit.php?id='.$row['id'].'&client_id='.$row['client_id'].''.$broker_id.'" data-id="'.$row['id'].'" class="btn btn-primary btn-sm editInvoiceBtn '. (($row['paid_inv']=="1")? "hide-element" :"") .'  '.$check_edit.'" title="Modifier Facture" ><span><i class="bi bi-pencil-square"></i></span></a>
                                                        <a href="javascript:void(0)" data-id="'.$row['id'].'"  class=" btn btn-danger btn-sm deleteInvoiceBtn '.$check_delete.' " title="Supprimer Facture" ><span><i class="bi bi-trash"></i></span></a>
                                                        
                                                    </td>
                                                </tr>';
                                                $number++;
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- table Invoice END -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Delete service modal -->
<div class="modal fade" id="deleteInvoiceModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class='modal-body'>
                <p class="text-center">Êtes-vous sûr de vouloir supprimer cet facture ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-danger deleteInvoiceModalBtn">Supprimer</button>
            </div>
        </div>
    </div>
</div>
<!-- Delete service modal end -->

<?php include 'footer.php'; ?>