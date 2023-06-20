<?php
    include 'includes/config.php';
    include 'functions.php';
    
    if(isset($_POST)){

        $nom = mysqli_real_escape_string($cnx,$_POST['brokerNom']);
        $prenom = mysqli_real_escape_string($cnx,$_POST['brokerPrenom']);
        $phone = mysqli_real_escape_string($cnx,$_POST['brokerTel']);
        $address = mysqli_real_escape_string($cnx,$_POST['brokerAdr']);
        $brokerIce = mysqli_real_escape_string($cnx,$_POST['brokerIce']);

        //Fetching all Brokers Title Data
        $fullBrokerName = $nom.' '.$prenom;
        $res = getBrokerData();
        $exist = false;
        while($row=mysqli_fetch_assoc($res)){
            if(strtolower(str_replace(" ","",$fullBrokerName)) == strtolower(str_replace(" ","",$row['nom'].$row['prenom'])))
            {
                $exist=true;
                break;
            }
        }

        if($exist)
        {
            $_SESSION['error'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>&nbsp;
                        <strong>'.ucfirst($fullBrokerName).' aleardy exists!</strong> please try another one.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';

            header("location:broker.php");exit();
        }else{

            $query = "INSERT INTO `broker`( `nom`,`prenom`, `phone`, `address`, `brokerIce`) VALUES ('$nom','$prenom','$phone','$address','$brokerIce')";
            $res = mysqli_query($cnx,$query);
    
            // $broker_id = mysqli_insert_id($cnx);
            $user_id = $_SESSION['user_id'];
            if($res)
            {
                userBroker_history($user_id,$fullBrokerName,'Add');
                $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill"></i>&nbsp;
                            <strong>Intermédiaire Added Successfully.</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
    
                header('location:broker-view.php');
            }
        }
                

    }else{
        header('location:broker-view.php');exit();
    }

?>