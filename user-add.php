<?php

    include 'includes/config.php';

    
    if(isset($_POST)){
        // extract();
        $prenom = mysqli_real_escape_string($cnx,$_POST['prenom']);
        $nom = mysqli_real_escape_string($cnx,$_POST['nom']);
        $email = mysqli_real_escape_string($cnx,$_POST['email']);
        $phone = mysqli_real_escape_string($cnx,$_POST['phone']);
        $username = mysqli_real_escape_string($cnx,$_POST['username']);
        $password = mysqli_real_escape_string($cnx,$_POST['password']);
        
        
        $query = "SELECT `username` FROM `users`;";
        $res = mysqli_query($cnx,$query);
        $exist = false;
        while($row = mysqli_fetch_assoc($res)){
            if($row["username"]==$username){
                $exist = true;
                break;
            }
        }
        if($exist){
            // alert message
            $_SESSION['error'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>&nbsp;
                        <strong>This Username aleardy exists!</strong> please try another Username.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                    header('location:user-create.php');exit();

        }else{
            if(isset($_POST['role'])){

                $query = "INSERT INTO `users`( `prenom`, `nom`, `email`, `tel`, `username`, `password`) VALUES ('$prenom','$nom','$email','$phone','$username','$password')";
                $res = mysqli_query($cnx,$query);
                $user_id;
                if($res){
                    $user_id = mysqli_insert_id($cnx);
                }

                $role_id = mysqli_real_escape_string($cnx,$_POST['role']);
                $query = "INSERT INTO `user_role`(`user_id`, `role_id`) VALUES ('$user_id','$role_id')";
                $res = mysqli_query($cnx,$query);

                $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i>&nbsp;
                        <strong>User Added Successfully.</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                header("location:user-list.php");
                
            }else{
                $_SESSION['error'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>&nbsp;
                        <strong class="text-center">Select a Role Please!</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                    header('location:user-create.php');exit();
            }

        }

    }else{
        header('location:user-list.php');exit();
    }

?>





