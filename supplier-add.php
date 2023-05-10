<?php
    include 'includes/config.php';
    include 'functions.php';
    
    if(isset($_POST)){

        $full_name = mysqli_real_escape_string($cnx,$_POST['supplierFullName']);
        $address = mysqli_real_escape_string($cnx,$_POST['supplierAdr']);
        $phone = mysqli_real_escape_string($cnx,$_POST['supplierPhone']);
        $category = $_POST['suppCatSelect'];

        //Fetching all Categories Title Data
        
        $res = getSupplierData();
        $exist = false;
        while($row=mysqli_fetch_assoc($res)){
            if(strtolower(str_replace(" ","",$full_name)) == strtolower(str_replace(" ","",$row['full_name'])))
            {
                $exist=true;
                break;
            }
        }

        
        if($exist)
        {
            $_SESSION['error'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>&nbsp;
                        <strong>'.ucfirst($full_name).' aleardy exists!</strong> please try another one.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';

            header("location:supplier.php");exit();
        }else{

            $query = "INSERT INTO `supplier`( `full_name`,`address`,`phone`,`cat_id`) VALUES ('$full_name','$address','$phone','$category')";
            $res = mysqli_query($cnx,$query);

            if($res)
            {
                $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill"></i>&nbsp;
                            <strong>Fournisseur Added Successfully.</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
    
                header('location:supplier-list.php');
            }
        }
                

    }else{
        header('location:supplier-list.php');exit();
    }

?>