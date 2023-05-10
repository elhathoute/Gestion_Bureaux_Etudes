<?php

    include 'includes/config.php';

    
    if(isset($_POST)){
        $roleName = mysqli_real_escape_string($cnx,$_POST["roleName"]);
        $query = "SELECT `role_name` FROM `roles`;";
        $res = mysqli_query($cnx,$query);
        $exist = false;
        while($row = mysqli_fetch_assoc($res)){
            if($row["role_name"]==$roleName){
                $exist = true;
                break;
            }
        }
        if($exist){
            // alert message
            $_SESSION['error'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>&nbsp;
                        <strong>This Role aleardy exists!</strong> please try another role name.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                    header('location:role-create.php');exit();

        }else{
            $query = "INSERT INTO `roles`(`role_name`) VALUES ('$roleName')";
            $res = mysqli_query($cnx,$query);
            $role_id;
            if($res){
                $role_id = mysqli_insert_id($cnx);
            }
            if(isset($_POST['perms'])){
                $perms = $_POST['perms'];
                foreach ($perms as $perm) {
                   $query = "INSERT INTO `role_perm`(`role_id`, `perm_id`) VALUES ('$role_id','$perm')";
                   $res = mysqli_query($cnx,$query);
                }
                $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i>&nbsp;
                        <strong>Role Added Successfully.</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                header("location:role-list.php");

            }
        }

    }else{
        header("location:role-list.php");exit();
    }

?>





