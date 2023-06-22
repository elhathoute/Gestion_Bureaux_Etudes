<?php

    include 'includes/config.php';
    include 'functions.php';
    if($_POST){
        if(isset($_POST['broker_id'])){
            $broker_id=$_POST['broker_id'];
            $MontantPaye=$_POST["purchasePrice"];
            $query = "UPDATE `broker` SET `sold` = (`sold` - $MontantPaye) WHERE `id`='$broker_id'";
            $res = mysqli_query($cnx,$query);
        }
        $P_name = mysqli_real_escape_string($cnx,$_POST["purchaseName"]);
        $P_price = mysqli_real_escape_string($cnx,$_POST["purchasePrice"]);
        $P_note = mysqli_real_escape_string($cnx,$_POST["purchaseNote"]);
        $P_number = sprintf("%03d", getPurchaseNumber()) . '/' . date('Y');

        $query = "INSERT INTO `purchase`(`id`, `P_number`, `name`, `price`, `note`) VALUES (null,'$P_number','$P_name','$P_price','$P_note');";
        $res = mysqli_query($cnx,$query);
        $p_id = mysqli_insert_id($cnx);
        if($res){
            $user_id = $_SESSION['user_id'];
            userPurchase_history($user_id,$p_id,'Add');
            // alert message
            $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i>&nbsp;
                        <strong>Achat Added Successfully.</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
        }
        header("location:purchase-list.php");

    }else{
        header("location:purchase-list.php");exit();
    }

?>
