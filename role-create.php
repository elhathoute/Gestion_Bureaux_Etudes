<?php
include 'header.php';

$check_showAll =  ($role->hasPerm('show all')) ? "":"hide-element";
?>

<?php
    if(isset($_SESSION["error"])){
        echo $_SESSION["error"];
        unset($_SESSION["error"]);
    }
?>
<div class="pagetitle">
    <h1>Role</h1>
</div>
<section class="section">
    <form action="role-add.php" method="POST">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Role information</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="roleNameText" class="form-label">Role Nom</label>
                                    <input type="text" class="form-control" id="roleNameText" name="roleName" placeholder="Role Name" required>
                                </div>
                            </div>
                        </div>
                        <h5 class="card-title fs-4 fw-light pb-0">Permissions</h5>
                        <div class="row">
                            <fieldset class="border-dashed rounded-3 my-2 p-2">
                                <legend class="float-none w-auto">Client</legend>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"  name="perms[]" id="CbShowClient">
                                            <label class="form-check-label" for="CbShowClient">
                                                Afficher
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ************ -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="2"  name="perms[]" id="CbCreateClient">
                                            <label class="form-check-label" for="CbCreateClient">
                                                Creer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="3"  name="perms[]" id="CbEditClient">
                                            <label class="form-check-label" for="CbEditClient">
                                                Modifier
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="4"  name="perms[]" id="CbDeleteClient">
                                            <label class="form-check-label" for="CbDeleteClient">
                                                Supprimer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    
                                </div>
                            </fieldset>
                            <!-- ******************* -->
                            <fieldset class="border-dashed rounded-3 my-2 p-2">
                                <legend class="float-none w-auto">Service</legend>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="5"  name="perms[]" id="CbShowService">
                                            <label class="form-check-label" for="CbShowService">
                                                Afficher
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ************ -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="6"  name="perms[]" id="CbCreateService">
                                            <label class="form-check-label" for="CbCreateService">
                                                Creer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="7"  name="perms[]" id="CbEditService">
                                            <label class="form-check-label" for="CbEditService">
                                                Modifier
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="8"  name="perms[]" id="CbDeleteService">
                                            <label class="form-check-label" for="CbDeleteService">
                                                Supprimer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    
                                </div>
                            </fieldset>
                            <!-- ****************************** -->

                            <fieldset class="border-dashed rounded-3 my-2 p-2">
                                <legend class="float-none w-auto">Intermédiaire</legend>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="37"  name="perms[]" id="CbShowBroker">
                                            <label class="form-check-label" for="CbShowBroker">
                                                Afficher
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ************ -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="38"  name="perms[]" id="CbCreateBroker">
                                            <label class="form-check-label" for="CbCreateBroker">
                                                Creer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="39"  name="perms[]" id="CbEditBroker">
                                            <label class="form-check-label" for="CbEditBroker">
                                                Modifier
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="40"  name="perms[]" id="CbDeleteBroker">
                                            <label class="form-check-label" for="CbDeleteBroker">
                                                Supprimer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    
                                </div>
                            </fieldset>
                            <!-- ****************************** -->

                            <fieldset class="border-dashed rounded-3 my-2 p-2">
                                <legend class="float-none w-auto">Devis</legend>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="9"  name="perms[]" id="CbShowDevis">
                                            <label class="form-check-label" for="CbShowDevis">
                                                Afficher
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="10" name="perms[]"  id="CbCreateDevis">
                                            <label class="form-check-label" for="CbCreateDevis">
                                                Creer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="11" name="perms[]"  id="CbEditDevis">
                                            <label class="form-check-label" for="CbEditDevis">
                                                Modifier
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="12" name="perms[]"  id="CbDeleteDevis">
                                            <label class="form-check-label" for="CbDeleteDevis">
                                                Supprimer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md my-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="17" name="perms[]"  id="CbExportDevis">
                                            <label class="form-check-label" for="CbExportDevis">
                                                Exporter
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                </div>
                            </fieldset>
                            <!-- ****************************** -->
                            <fieldset class="border-dashed rounded-3 my-2 p-2">
                                <legend class="float-none w-auto">Facture</legend>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="23"  name="perms[]" id="CbShowInvoice">
                                            <label class="form-check-label" for="CbShowInvoice">
                                                Afficher
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="24" name="perms[]"  id="CbCreateInvoice">
                                            <label class="form-check-label" for="CbCreateInvoice">
                                                Creer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="25" name="perms[]"  id="CbEditInvoice">
                                            <label class="form-check-label" for="CbEditInvoice">
                                                Modifier
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="26" name="perms[]"  id="CbDeleteInvoice">
                                            <label class="form-check-label" for="CbDeleteInvoice">
                                                Supprimer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md my-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="27" name="perms[]"  id="CbExportInvoice">
                                            <label class="form-check-label" for="CbExportInvoice">
                                                Exporter
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                </div>
                            </fieldset>
                            <!-- ****************************** -->
                            <fieldset class="border-dashed rounded-3 my-2 p-2">
                                <legend class="float-none w-auto">Paiement</legend>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="28"  name="perms[]" id="CbShowPayment">
                                            <label class="form-check-label" for="CbShowPayment">
                                                Afficher
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="29" name="perms[]"  id="CbCreatePayment">
                                            <label class="form-check-label" for="CbCreatePayment">
                                                Creer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                </div>
                            </fieldset>

                            <!-- ****************************** -->
                            <fieldset class="border-dashed rounded-3 my-2 p-2">
                                <legend class="float-none w-auto">Situation</legend>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="32" name="perms[]"  id="CbShowSituation">
                                            <label class="form-check-label" for="CbShowSituation">
                                                Afficher
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                </div>
                            </fieldset>


                            <!-- ****************************** -->
                            <fieldset class="border-dashed rounded-3 my-2 p-2">
                                <legend class="float-none w-auto">Achat</legend>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="33"  name="perms[]" id="CbShowPurchase">
                                            <label class="form-check-label" for="CbShowPurchase">
                                                Afficher
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="34" name="perms[]"  id="CbCreatePurchase">
                                            <label class="form-check-label" for="CbCreatePurchase">
                                                Creer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="35" name="perms[]"  id="CbEditPurchase">
                                            <label class="form-check-label" for="CbEditPurchase">
                                                Modifier
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="36" name="perms[]"  id="CbDeletePurchase">
                                            <label class="form-check-label" for="CbDeletePurchase">
                                                Supprimer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    
                                </div>
                            </fieldset>


                            <!-- ****************************** -->
                            <fieldset class="border-dashed rounded-3 my-2 p-2">
                                <legend class="float-none w-auto">Role</legend>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="13" name="perms[]"  id="CbShowRole">
                                            <label class="form-check-label" for="CbShowRole">
                                                Afficher
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3 <?=$check_showAll;?>">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="14" name="perms[]"  id="CbCreateRole">
                                            <label class="form-check-label" for="CbCreateRole">
                                                Creer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="15" name="perms[]"  id="CbEditRole">
                                            <label class="form-check-label" for="CbEditRole">
                                                Modifier
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="16" name="perms[]"  id="CbDeleteRole">
                                            <label class="form-check-label" for="CbDeleteRole">
                                                Supprimer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                </div>
                            </fieldset>
                            <!-- ****************************** -->
                            <fieldset class="border-dashed rounded-3 my-2 p-2">
                                <legend class="float-none w-auto">Utilisateur</legend>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="18" name="perms[]"  id="CbShowUser">
                                            <label class="form-check-label" for="CbShowUser">
                                                Afficher
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="19" name="perms[]"  id="CbCreateUser">
                                            <label class="form-check-label" for="CbCreateUser">
                                                Creer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="20" name="perms[]"  id="CbEditUser">
                                            <label class="form-check-label" for="CbEditUser">
                                                Modifier
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="21" name="perms[]"  id="CbDeleteUser">
                                            <label class="form-check-label" for="CbDeleteUser">
                                                Supprimer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                </div>
                            </fieldset>

                            <!-- ****************************** -->
                            <fieldset class="border-dashed rounded-3 my-2 p-2 <?=$check_showAll;?>">
                                <legend class="float-none w-auto">Notification</legend>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="22" name="perms[]"  id="CbShowHistory">
                                            <label class="form-check-label" for="CbShowHistory">
                                                Afficher
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                </div>
                            </fieldset>
                            
                            <!-- ****************************** -->
                            <fieldset class="border-dashed rounded-3 my-2 p-2">
                                <legend class="float-none w-auto">Historiques</legend>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="31" name="perms[]"  id="CbShowHistory">
                                            <label class="form-check-label" for="CbShowHistory">
                                                Afficher
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                </div>
                            </fieldset>


                            

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 my-3">
                <input type="submit" name="submit" id="rl_add" value="Create Role" class="btn btn-success float-end" title="Créer Role">
            </div>
        </div>
    </form>
</section>


<?php include 'footer.php'; ?>