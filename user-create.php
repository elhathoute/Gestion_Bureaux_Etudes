<?php
include 'header.php';

?>


<?php
if(isset($_SESSION["error"])){
    echo $_SESSION["error"];
    unset($_SESSION["error"]);
}

?>

<div class="pagetitle">
    <h1>Ajouter un utilisateur</h1>
</div>
<!-- <?=$_SESSION['user_id'];?> -->
<section class="section">
    <form action="user-add.php" method="POST">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Utilisateur information</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="prenomText" class="form-label">Prenom</label>
                                    <input type="text" class="form-control" name="prenom" id="prenomText" placeholder="Prenom" required>
                                </div>
                                <!-- ********* -->
                                <div class="mb-3">
                                    <label for="nomText" class="form-label">Nom</label>
                                    <input type="text" class="form-control" name="nom" id="nomText" placeholder="Nom" required>
                                </div>
                                <!-- ********* -->
                                <div class="mb-3">
                                    <label for="emailText" class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" id="emailText" placeholder="Email" required>
                                </div>
                                <!-- ********* -->
                                <div class="mb-3">
                                    <label for="userPhoneText" class="form-label">Téléphone</label>
                                    <input type="text" class="form-control" name="phone" id="userPhoneText" placeholder="Phone" required>
                                </div>
                                <!-- ********* -->
                                <div class="mb-3">
                                    <label for="usernameText" class="form-label">Nom d'utilisateur</label>
                                    <input type="text" class="form-control" name="username" id="usernameText" placeholder="Username" required>
                                </div>
                                <!-- ********* -->
                                <div class="mb-3">
                                    <label for="passwordText" class="form-label">Mot de passe</label>
                                    <input type="password" class="form-control" name="password" id="passwordText" placeholder="Password" required>
                                </div>
                                <!-- ********* -->
                                <div class="mb-3">
                                    <label for="roleSelect" class="form-label">Role</label>
                                    <select class="form-select" aria-label="Default select example" name="role" id="roleSelect">
                                        <option selected disabled></option>
                                        <?php
                                            $res = getAllRoles();
                                            while($row=mysqli_fetch_assoc($res)){
                                                echo '<option value="'.$row['id'].'">'.ucfirst($row['role_name']).'</option>';
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
                <input type="submit" name="submit" id="usr_add" value="Create User" class="btn btn-success float-end" title="Créer User">
            </div>
        </div>
    </form>
</section>


<?php include 'footer.php'; ?>