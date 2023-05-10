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

?>


<div class="row my-3">
    <div class="pagetitle col-md-8">
        <h1>Liste des utilisateur</h1>
    </div>
    <div class="col-md-4 ">
        <a href='user-create.php' class="btn btn-primary float-end" id="add-user" title="Créer User"> Add User</a>
    </div>
</div>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <!-- <h3 class="card-title">Devis information</h3> -->
                    <div class="my-4"></div>
                    <div class="tab-content" id="users-content">
                        <!-- table User -->
                        <div class="">
                            <div>
                                <table id="usersTable" class="table table-hover table-bordered table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th width="300">
                                                <a href="#">Nom d'utilisateur</a>
                                            </th>
                                            <th>
                                                <a href="#">Role</a>
                                            </th>
                                            <th>
                                                <a href="#">Status</a>
                                            </th>
                                            <th>
                                                <a href="#">Dernière connexion</a>
                                            </th>
                                            <th width="150">
                                                <a href="#">Action</a>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $user_id = $_SESSION['user_id'];
                                            $query = "CALL `sp_getAllUsers`('$user_id');";
                                            $res = mysqli_query($cnx,$query);
                                            $check_edit =  ($role->hasPerm('edit user')) ? "":"hide-element";
                                            $check_delete = ($role->hasPerm('delete user')) ? "":"hide-element";
                                            while($row = mysqli_fetch_assoc($res)){
                                                $html = '';
                                                $html .= '<tr>';
                                                $html .= '<td>'.ucwords($row["prenom"]." ".$row['nom']).'</td>';
                                                $html .= '<td><span class="badge text-bg-secondary">'.strtolower($row["role_name"]).'</span>&nbsp;&nbsp;</td>';
                                                $status = ($row['status']=='1')?"<span class='badge text-bg-success'>active</span>":"<span class='badge text-bg-danger'>inactive</span>";
                                                $html .= '<td>'.$status.'</td>';
                                                $html .= '<td>'.$row['last_login'].'</td>';
                                                $html .= '<td>
                                                            <a href="user-edit.php?u_id='.$row["id"].'" data-id="" class="btn btn-primary btn-sm editUserBtn '.$check_edit.' " title="Modifier User"><span><i class="bi bi-pencil-square"></i></span></a>
                                                            <a href="javascript:void(0)" data-id="'.$row["id"].'" class="btn btn-danger btn-sm deleteUserBtn '.$check_delete.' " title="Supprimer User"><span><i class="bi bi-trash"></i></span></a>
                                                        </td>';
                                                $html .= '</tr>';
                                                echo $html;
                                            }
                                        
                                        ?>

                                        <!-- <tr>
                                            <td>Azeddine Taki</td>
                                            <td><span class="badge text-bg-secondary">admin</span>&nbsp;&nbsp;</td>
                                            <td>
                                                <a href="#" data-id="" class="btn btn-primary btn-sm editDevisBtn"><span><i class="bi bi-pencil-square"></i></span></a>
                                                <a href="javascript:void(0)" data-id="" class="btn btn-danger btn-sm deleteDevisBtn"><span><i class="bi bi-trash"></i></span></a>
                                            </td>
                                        </tr> -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- table User END -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<!-- Delete user modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class='modal-body'>
                <p class="text-center">Êtes-vous sûr de vouloir supprimer cet utilisateur ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-danger deleteUserModalBtn">Supprimer</button>
            </div>
        </div>
    </div>
</div>
<!-- Delete user modal end -->






<?php include 'footer.php'; ?>