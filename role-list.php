<?php
include 'header.php';

?>

<?php
    if(isset($_SESSION["success"])){
        echo $_SESSION["success"];
        unset($_SESSION["success"]);
    }
    if(isset($_SESSION["error"])){
        echo $_SESSION["error"];
        unset($_SESSION["error"]);
    }

$check_create =  ($role->hasPerm('create role')) ? "":"hide-element";
?>

<div class="row my-3">
    <div class="pagetitle col-md-8">
        <h1>Liste des Roles </h1>
    </div>
    <div class="col-md-4 <?=$check_create;?>">
        <a href='role-create.php' id="rl_create" class="btn btn-primary float-end" title="Créer Role"> Ajouter Role</a>
    </div>
</div>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <!-- <h3 class="card-title">Devis information</h3> -->
                    <div class="my-4"></div>
                    <div class="tab-content" id="roles-content">
                        <!-- table Role -->
                        <div class="">
                            <div>
                                <table id="rolesTable" class="table table-hover table-bordered table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th width="200">
                                                <a href="#">Role Nom</a>
                                            </th>
                                            <th>
                                                <a href="#">Permissions</a>
                                            </th>
                                            <th width="150">
                                                <a href="#">Action</a>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                            $res = getAllRoles();
                                            $check_edit =  ($role->hasPerm('edit role')) ? "":"hide-element";
                                            $check_delete = ($role->hasPerm('delete role')) ? "":"hide-element";
                                            while($row = mysqli_fetch_assoc($res)){
                                                $role_id = $row["id"];
                                                $html = '<tr>';
                                                $html .= '<td>'.ucfirst($row['role_name']).'</td>';
                                                $html .= '<td>';
                                                $permRes = getPerm($role_id);
                                                while($permRow=mysqli_fetch_assoc($permRes)){
                                                    $html .= '<span class="badge text-bg-secondary">'.ucfirst($permRow["perm_desc"]).'</span>&nbsp;&nbsp;';
                                                }
                                                $html .= '</td>';
                                                $html .= '<td>
                                                              <a href="role-edit.php?r_id='.$role_id.'" data-id="" class="btn btn-primary btn-sm editRoleBtn '.$check_edit.' " title="Modifier Role"><span><i class="bi bi-pencil-square"></i></span></a>
                                                              <a href="javascript:void(0)" data-id="'.$role_id.'" class="btn btn-danger btn-sm deleteRoleBtn '.$check_delete.' " title="Supprimer Role"><span><i class="bi bi-trash"></i></span></a>
                                                          </td>';
                                                $html .= '</tr>';
                                                echo $html;
                                            }
                                        
                                        ?>

                                        <!-- <tr>
                                            <td>Admin</td>
                                            <td><span class="badge text-bg-secondary">create devis</span>&nbsp;&nbsp;</td>
                                            <td>
                                                <a href="role-edit?r_id=" data-id="" class="btn btn-primary btn-sm editRoleBtn"><span><i class="bi bi-pencil-square"></i></span></a>
                                                <a href="javascript:void(0)" data-id="" class="btn btn-danger btn-sm deleteRoleBtn"><span><i class="bi bi-trash"></i></span></a>
                                            </td>
                                        </tr> -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- table Role END -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Delete role modal -->
<div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class='modal-body'>
                <p class="text-center">Êtes-vous sûr de vouloir supprimer ce role ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-danger deleteRoleModalBtn">Supprimer</button>
            </div>
        </div>
    </div>
</div>
<!-- Delete role modal end -->







<?php include 'footer.php'; ?>