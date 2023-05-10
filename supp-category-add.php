<?php
    include 'includes/config.php';
    include 'functions.php';
    
    if(isset($_POST)){

        $title = mysqli_real_escape_string($cnx,$_POST['catTitle']);
        $type = mysqli_real_escape_string($cnx,$_POST['catType']);

        //Fetching all Categories Title Data
        
        $res = getSuppCatData();
        $exist = false;
        while($row=mysqli_fetch_assoc($res)){
            if(strtolower(str_replace(" ","",$title)) == strtolower(str_replace(" ","",$row['title'])))
            {
                $exist=true;
                break;
            }
        }

        
        if($exist)
        {
            $_SESSION['error'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>&nbsp;
                        <strong>'.ucfirst($title).' aleardy exists!</strong> please try another one.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';

            header("location:category.php");exit();
        }else{

            $query = "INSERT INTO `supp_category`( `title`,`type`) VALUES ('$title','$type')";
            $res = mysqli_query($cnx,$query);

            if($res)
            {
                $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill"></i>&nbsp;
                            <strong>Catégorie Added Successfully.</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
    
                header('location:category-list.php');
            }
        }
                

    }else{
        header('location:category-list.php');exit();
    }

?>