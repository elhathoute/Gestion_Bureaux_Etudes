<?php
include 'header.php';


function checkPerm($value){
    if(isset($_GET['r_id'])){
        $role_id = $_GET['r_id'];
        $perms = array();
        $res = getPerm($role_id);
        $permsId = array();
        while($row = mysqli_fetch_assoc($res)){
            $permsId[] = $row['id'];
        }
        if(count($permsId)!=0){
            return in_array($value,$permsId)?"checked":"";
        }
    }
}
//get role name
if(isset($_GET['r_id'])){
    $role_id = $_GET['r_id'];
    $query = "SELECT * FROM `roles` WHERE `id`='$role_id'";
    $res = mysqli_query($cnx,$query);
    $row = mysqli_fetch_assoc($res);
}
$check_showAll =  ($role->hasPerm('show all')) ? "":"hide-element";
?>

<?php
    // if(isset($_SESSION["error"])){
    //     echo $_SESSION["error"];
    //     unset($_SESSION["error"]);
    // }
?>
<div class="pagetitle">
    <h1>Modifer Role</h1>
</div>
<section class="section">
    <form action="role-update.php" method="POST">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Role information</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="roleNameText" class="form-label">Role Nom</label>
                                    <input type="text" class="form-control" id="roleNameText"  name="roleName" placeholder="Role Name" value="<?=$row["role_name"]; ?>" required>
                                    <input type="hidden" name="old_role_name" value="<?=$row['role_name'];?>">
                                    <input type="hidden" name="role_id" value="<?=$role_id;?>">
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
                                            <input class="form-check-input" type="checkbox" value="1"  name="perms[]" id="CbShowClient" <?=checkPerm(1)?> >
                                            <label class="form-check-label" for="CbShowClient">
                                                Afficher
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ************ -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="2"  name="perms[]" id="CbCreateClient" <?=checkPerm(2)?>>
                                            <label class="form-check-label" for="CbCreateClient">
                                                Creer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="3"  name="perms[]" id="CbEditClient" <?=checkPerm(3)?>>
                                            <label class="form-check-label" for="CbEditClient">
                                                Modifier
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="4"  name="perms[]" id="CbDeleteClient" <?=checkPerm(4)?>>
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
                                            <input class="form-check-input" type="checkbox" value="5"  name="perms[]" id="CbShowService" <?=checkPerm(5)?>>
                                            <label class="form-check-label" for="CbShowService">
                                                Afficher
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ************ -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="6"  name="perms[]" id="CbCreateService" <?=checkPerm(6)?>>
                                            <label class="form-check-label" for="CbCreateService">
                                                Creer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="7"  name="perms[]" id="CbEditService" <?=checkPerm(7)?>>
                                            <label class="form-check-label" for="CbEditService">
                                                Modifier
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="8"  name="perms[]" id="CbDeleteService" <?=checkPerm(8)?>>
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
                                            <input class="form-check-input" type="checkbox" value="37"  name="perms[]" id="CbShowBroker" <?=checkPerm(37)?>>
                                            <label class="form-check-label" for="CbShowBroker">
                                                Afficher
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ************ -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="38"  name="perms[]" id="CbCreateBroker" <?=checkPerm(38)?>>
                                            <label class="form-check-label" for="CbCreateBroker">
                                                Creer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="39"  name="perms[]" id="CbEditBroker" <?=checkPerm(39)?>>
                                            <label class="form-check-label" for="CbEditBroker">
                                                Modifier
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="40"  name="perms[]" id="CbDeleteBroker" <?=checkPerm(40)?>>
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
                                            <input class="form-check-input" type="checkbox" value="9"  name="perms[]" id="CbShowDevis" <?=checkPerm(9)?>>
                                            <label class="form-check-label" for="CbShowDevis">
                                                Afficher
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="10" name="perms[]"  id="CbCreateDevis" <?=checkPerm(10)?>>
                                            <label class="form-check-label" for="CbCreateDevis">
                                                Creer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="11" name="perms[]"  id="CbEditDevis" <?=checkPerm(11)?>>
                                            <label class="form-check-label" for="CbEditDevis">
                                                Modifer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="12" name="perms[]"  id="CbDeleteDevis" <?=checkPerm(12)?>>
                                            <label class="form-check-label" for="CbDeleteDevis">
                                                Supprimer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md my-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="17" name="perms[]"  id="CbExportDevis" <?=checkPerm(17)?>>
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
                                            <input class="form-check-input" type="checkbox" value="23"  name="perms[]" id="CbShowInvoice" <?=checkPerm(23);?>>
                                            <label class="form-check-label" for="CbShowInvoice">
                                                Afficher
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="24" name="perms[]"  id="CbCreateInvoice" <?=checkPerm(24);?>>
                                            <label class="form-check-label" for="CbCreateInvoice">
                                                Creer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="25" name="perms[]"  id="CbEditInvoice" <?=checkPerm(25);?>>
                                            <label class="form-check-label" for="CbEditInvoice">
                                                Modifier
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="26" name="perms[]"  id="CbDeleteInvoice" <?=checkPerm(26);?>>
                                            <label class="form-check-label" for="CbDeleteInvoice">
                                                Supprimer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md my-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="27" name="perms[]"  id="CbExportInvoice" <?=checkPerm(27);?>>
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
                                            <input class="form-check-input" type="checkbox" value="28"  name="perms[]" id="CbShowPayment" <?=checkPerm(28);?>>
                                            <label class="form-check-label" for="CbShowPayment">
                                                Afficher
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="29" name="perms[]"  id="CbCreatePayment" <?=checkPerm(29);?>>
                                            <label class="form-check-label" for="CbCreatePayment">
                                                Creer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="41" name="perms[]"  id="CbCreatePaymentChecking" <?=checkPerm(41);?>>
                                            <label class="form-check-label" for="CbCreatePaymentChecking">
                                            paiement par chèque
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <!-- ****************************** -->
                            <fieldset class="border-dashed rounded-3 my-2 p-2">
                                <legend class="float-none w-auto">Situation</legend>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="32" name="perms[]"  id="CbShowSituation" <?=checkPerm(32)?>>
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
                                            <input class="form-check-input" type="checkbox" value="33"  name="perms[]" id="CbShowPurchase" <?=checkPerm(33)?>>
                                            <label class="form-check-label" for="CbShowPurchase">
                                                Afficher
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="34" name="perms[]"  id="CbCreatePurchase" <?=checkPerm(34)?>>
                                            <label class="form-check-label" for="CbCreatePurchase">
                                                Creer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="35" name="perms[]"  id="CbEditPurchase" <?=checkPerm(35)?>>
                                            <label class="form-check-label" for="CbEditPurchase">
                                                Modifier
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="36" name="perms[]"  id="CbDeletePurchase" <?=checkPerm(36)?>>
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
                                            <input class="form-check-input" type="checkbox" value="13" name="perms[]"  id="CbShowRole" <?=checkPerm(13)?>>
                                            <label class="form-check-label" for="CbShowRole">
                                                Afficher
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3 <?=$check_showAll;?>">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="14" name="perms[]"  id="CbCreateRole" <?=checkPerm(14)?>>
                                            <label class="form-check-label" for="CbCreateRole">
                                                Creer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="15" name="perms[]"  id="CbEditRole" <?=checkPerm(15)?>>
                                            <label class="form-check-label" for="CbEditRole">
                                                Modifier
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="16" name="perms[]"  id="CbDeleteRole" <?=checkPerm(16)?>>
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
                                            <input class="form-check-input" type="checkbox" value="18" name="perms[]"  id="CbShowUser" <?=checkPerm(18)?>>
                                            <label class="form-check-label" for="CbShowUser">
                                                Afficher
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="19" name="perms[]"  id="CbCreateUser" <?=checkPerm(19)?>>
                                            <label class="form-check-label" for="CbCreateUser">
                                                Creer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="20" name="perms[]"  id="CbEditUser" <?=checkPerm(20)?>>
                                            <label class="form-check-label" for="CbEditUser">
                                                Modifier
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="21" name="perms[]"  id="CbDeleteUser" <?=checkPerm(21)?>>
                                            <label class="form-check-label" for="CbDeleteUser">
                                                Supprimer
                                            </label>
                                        </div>
                                    </div>
                                    <!-- ********** -->
                                </div>
                            </fieldset>

                            <!-- ****************************** -->
                            <fieldset class="border-dashed rounded-3 my-2 p-2">
                                <legend class="float-none w-auto">Notification</legend>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="22" name="perms[]"  id="CbShowHistory" <?=checkPerm(22)?>>
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
                                <legend class="float-none w-auto">Historques</legend>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="31" name="perms[]"  id="CbShowHistory" <?=checkPerm(31)?>>
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
                <input type="submit" name="submit" value="Update Role" class="btn btn-success float-end" id="updt_rl">
            </div>
        </div>
    </form>
</section>


<?php include 'footer.php'; ?>