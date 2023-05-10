<?php
    include 'includes/config.php';

    
    if($_POST){
        $old_role_name = $_POST['old_role_name'];
        $roleName = $_POST["roleName"];
        $query = "SELECT `role_name` FROM `roles`;";
        $res = mysqli_query($cnx,$query);
        $exist = false;
        while($row = mysqli_fetch_assoc($res)){
            if($row["role_name"]==$roleName && $old_role_name != $roleName){
                $exist = true;
                break;
            }
        }
        if($exist){
            // alert message
            $_SESSION['error'] = '<div class="alert alert-warning alert-dismissible fade show"  role="alert">
                        <strong>This Role name aleardy exists!</strong> please try another role name.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                    header('location:role-list.php');exit();

        }else{
            
            $role_id = $_POST['role_id'];
            if($res && isset($_POST['perms'])){
                $query = "UPDATE `roles` SET `role_name`='$roleName' WHERE `id`=$role_id";
                mysqli_query($cnx,$query);
                // ********************
                $query = 'DELETE FROM `role_perm` WHERE `role_id`='.$role_id.'';
                $res  = mysqli_query($cnx,$query);
                $perms = $_POST['perms'];
                foreach ($perms as $perm) {
                    $query = "INSERT INTO `role_perm`(`role_id`, `perm_id`) VALUES ('$role_id','$perm')";
                    $res = mysqli_query($cnx,$query);
                }
                $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i>&nbsp;
                        <strong>Role Updated Successfully.</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                header("location:role-list.php");
    
            
            }else{
                $_SESSION['error'] = '<div class="alert alert-warning alert-dismissible fade show"  role="alert">
                        <strong>Select at least one box!</strong> Try again Please.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                header('location:role-list.php');exit();
            }
        }
    }else{
        header('location:role-list.php');exit();
    }