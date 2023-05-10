<?php

    include 'includes/config.php';
    include 'functions.php';

    
    if($_POST){
        $P_name = mysqli_real_escape_string($cnx,$_POST["purchaseName"]);
        $P_price = mysqli_real_escape_string($cnx,$_POST["purchasePrice"]);
        $P_note = mysqli_real_escape_string($cnx,$_POST["purchaseNote"]);
        $p_id = $_POST["p_id"];

        $query = "UPDATE `purchase` SET `name`='$P_name',`price`='$P_price',`note`='$P_note' WHERE `id`='$p_id' ;";
        $res = mysqli_query($cnx,$query);
        
        if($res){
            $user_id = $_SESSION['user_id'];
            userPurchase_history($user_id,$p_id,'Update');
            // alert message
            $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i>&nbsp;
                        <strong>Achat updated Successfully.</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
        }
        header("location:purchase-list.php");

    }else{
        header("location:purchase-list.php");exit();
    }

?>





