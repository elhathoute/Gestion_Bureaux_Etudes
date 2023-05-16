<?php
    include 'includes/config.php';
    include 'functions.php';
    
    if($_POST){
        extract($_POST);
        $client_type = $_POST['client-type'];
        $query ='';
        $type = '';
        if($client_type == "individual"){
            $type = "individual";
            // add to individual table
            $firstName = trim(mysqli_real_escape_string($cnx,$prenom));
            $lastName =trim(mysqli_real_escape_string($cnx,$nom));
            $phone = mysqli_real_escape_string($cnx,$phone);
            $email = mysqli_real_escape_string($cnx,$email);
            $adr = mysqli_real_escape_string($cnx,$address);

            $query = "INSERT INTO `client_individual`(`prenom`, `nom`, `email`, `tel`, `address`, `solde`) VALUES ('$firstName','$lastName','$email','$phone','$adr','0')";
            // add some sort of success alert
            // ...
        }else if($client_type == "entreprise"){
            $type = "entreprise";
            // add to entreprise table 
            $ent_nom = trim(mysqli_real_escape_string($cnx,$entNom));
            $ice = mysqli_real_escape_string($cnx,$ICE);
            $ent_phone = mysqli_real_escape_string($cnx,$entPhone);
            $ent_email = mysqli_real_escape_string($cnx,$entEmail);
            $ent_adr = mysqli_real_escape_string($cnx,$entAddress);

            $query = "INSERT INTO `client_entreprise`( `nom`, `ICE`, `email`, `tel`, `address`, `solde`) VALUES ('$ent_nom',' $ice','$ent_email','$ent_phone',' $ent_adr','0')";
        }
        $res = mysqli_query($cnx,$query);
        $cl_id = mysqli_insert_id($cnx);
        $user_id = $_SESSION['user_id'];
        userClient_history($user_id,$cl_id,$type,'Add');
        if($res){
            $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i>&nbsp;
                        <strong>Customer Added Successfully.</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
            header('location:customer-view.php');
        }
        // print_r($_POST);
    }else{
        header("location:customer-view.php");exit();
    }

?>