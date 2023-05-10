<?php
    include 'includes/config.php';

    
    if($_POST){
        $old_username = $_POST['old_username'];
        $username = mysqli_real_escape_string($cnx,$_POST['username']);
        $query = "SELECT `username` FROM `users`;";
        $res = mysqli_query($cnx,$query);
        $exist = false;
        while($row = mysqli_fetch_assoc($res)){
            if($row["username"]==$username && $old_username != $username){
                $exist = true;
                break;
            }
        }
        if($exist){
            // alert message
            $_SESSION['error'] = '<div class="alert alert-warning alert-dismissible fade show"  role="alert">
                        <strong>This Username aleardy exists!</strong> please try another username.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                    header('location:user-list.php');exit();

        }else{
            $user_id = $_POST['user_id'];
            $prenom = mysqli_real_escape_string($cnx,$_POST['prenom']);
            $nom = mysqli_real_escape_string($cnx,$_POST['nom']);
            $email = mysqli_real_escape_string($cnx,$_POST['email']);
            $phone = mysqli_real_escape_string($cnx,$_POST['phone']);
            $password = mysqli_real_escape_string($cnx,$_POST['password']);
            $userStatus = ($_POST['userStatus']=="1") ? 1:0;
            $query = "UPDATE `users` SET `prenom`='$prenom',`nom`='$nom',`email`='$email',`tel`='$phone',`username`='$username',`password`='$password',`status`='$userStatus' WHERE `id`=$user_id";
            mysqli_query($cnx,$query);
            // ********************
            $role_id = mysqli_real_escape_string($cnx,$_POST['role']);
            $query = "UPDATE `user_role` SET `role_id`='$role_id' WHERE `user_id` = $user_id";
            $res  = mysqli_query($cnx,$query);
            
            $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i>&nbsp;
                    <strong>User Updated Successfully.</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            header("location:user-list.php");

        }
    }else{
        header("location:user-list.php");exit();
    }