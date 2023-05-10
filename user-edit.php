<?php
include 'header.php';


$user_id = $_GET['u_id'];
$query = "SELECT * FROM `users` WHERE `id`='$user_id'";
$res = mysqli_query($cnx,$query);
$row = mysqli_fetch_assoc($res);

?>




<form action="user-update.php" method="POST">
    <div class="row">
        <div class="pagetitle col-md-8">
            <h1>Modifer un utilisateur</h1>
        </div>
        <div class="col-md-4">
            <select class="form-select float-end my-1" name="userStatus" id="userStatusDropdown">
                <option value="1" <?php if($row['status']=='1'){echo 'selected';} ?>>Active</option>
                <option value="0" <?php if($row['status']=='0'){echo 'selected';} ?> >Inactive</option>
            </select>
        </div>
    </div>
    <!-- <?=$_SESSION['user_id'];?> -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Utilisateur information</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="prenomText" class="form-label">Prenom</label>
                                    <input type="text" class="form-control" name="prenom" id="prenomText" value="<?=$row['prenom']?>" placeholder="Prenom" required>
                                    <input type="hidden" name="user_id" value="<?=$user_id?>" >
                                </div>
                                <!-- ********* -->
                                <div class="mb-3">
                                    <label for="nomText" class="form-label">Nom</label>
                                    <input type="text" class="form-control" name="nom" id="nomText" value="<?=$row['nom']?>" placeholder="Nom" required>
                                </div>
                                <!-- ********* -->
                                <div class="mb-3">
                                    <label for="emailText" class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" id="emailText" value="<?=$row['email']?>" placeholder="Email" required>
                                </div>
                                <!-- ********* -->
                                <div class="mb-3">
                                    <label for="userPhoneText" class="form-label">Téléphone</label>
                                    <input type="text" class="form-control" name="phone" id="userPhoneText" value="<?=$row['tel']?>" placeholder="Phone" required>
                                </div>
                                <!-- ********* -->
                                <div class="mb-3">
                                    <label for="usernameText" class="form-label">Nom d'utilisateur</label>
                                    <input type="text" class="form-control" name="username" id="usernameText" value="<?=$row['username']?>" placeholder="Username" required>
                                    <input type="hidden" name="old_username" value="<?=$row['username']?>">
                                </div>
                                <!-- ********* -->
                                <div class="mb-3">
                                    <label for="passwordText" class="form-label">Mot de passe</label>
                                    <input type="password" class="form-control" name="password" id="passwordText" value="<?=$row['password']?>" placeholder="Password" required>
                                </div>
                                <!-- ********* -->
                                <div class="mb-3">
                                    <label for="roleSelect" class="form-label">Role</label>
                                    <select class="form-select" aria-label="Default select example" name="role" id="roleSelect">
                                        <option selected disabled></option>
                                        <?php
                                            $selected_role_id = getUserRoleId($user_id);
                                            $res = getAllRoles();
                                            while($row=mysqli_fetch_assoc($res)){
                                                $selected = ($row['id']==$selected_role_id) ? "selected":"";
                                                echo '<option value="'.$row['id'].'" '.$selected.' >'.$row['role_name'].'</option>';
                                            }
                                        
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 my-3">
                <input type="submit" name="submit" value="Update User" class="btn btn-success float-end">
            </div>
        </div>
    </section>
</form>


<?php include 'footer.php'; ?>