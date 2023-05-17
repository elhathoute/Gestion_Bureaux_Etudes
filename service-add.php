<?php
    include 'includes/config.php';
    include 'functions.php';
    
    if(isset($_POST)){
        extract($_POST);
        $title = mysqli_real_escape_string($cnx,$serviceTitle);
        $price = mysqli_real_escape_string($cnx,$servicePrice);
        $ref = mysqli_real_escape_string($cnx,$serviceRef);

        //Fetching all Services Title Data
        $res = getServiceData();
        $exist = false;
        while($row=mysqli_fetch_assoc($res)){
            if(strtolower(str_replace(" ","",$title)) == strtolower(str_replace(" ","",$row["title"]))){
                $exist=true;
                break;
            }
        }

        
        if($exist){
            $_SESSION['error'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>&nbsp;
                        <strong>Ce titre existe déjà!</strong> S`il vous plaît essayer un autre titre.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';

            header("location:services.php");exit();
        }else{

            $query = "INSERT INTO `service`( `title`,`ref`) VALUES ('$title','$ref')";
            // add some sort of success alert
            // ...
    
            $res = mysqli_query($cnx,$query);
    
            // $service_id = mysqli_insert_id($cnx);
            $user_id = $_SESSION['user_id'];
            if($res){
                userService_history($user_id,$title,'Add');
                $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill"></i>&nbsp;
                            <strong>Service Added Successfully.</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
    
                header('location:services-view.php');
            }
        }
                

    }else{
        header('location:services-view.php');exit();
    }

?>