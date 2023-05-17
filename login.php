<?php
    include "includes/config.php";
    include 'functions.php';

        if($_POST && $_POST['username'] != "" && $_POST['password'] != ""){
      
            $username = mysqli_real_escape_string($cnx,$_POST["username"]);
            $password = mysqli_real_escape_string($cnx,$_POST["password"]);
    
            $query= "SELECT * FROM `users` WHERE username='$username' AND `password` = '$password'";
            $res=mysqli_query($cnx,$query);
            if($res && (mysqli_num_rows($res)==1) ){
                $row = mysqli_fetch_assoc($res);
                if($row['status'] != "1"){
                    $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show"  role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <strong>Votre compte est inactif !</strong> Veuillez contacter votre administrateur.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                    header('location:index.php');
                    
                }else{
                    //storing the username
                    $_SESSION["user"] = $username;
                    //storing the id
                   
                    $_SESSION['user_id'] = $row["id"];
                    if(isset($_POST["remember-me"])){
                        // cookie of usename
                        setcookie("logged_in",$username,time()+(60*60*24));
                         //  cookie of password
                        setcookie("logged_in_password",$password,time()+(60*60*24));
                    }
                    $current_date = date('Y-m-d H:i:s');
                    last_login($row['id'],$current_date);
                    header("location:dashboard.php");exit();
                }
            }else{
                $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show"  role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Votre nom utilisateur ou mot de passe incorrect.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                header('location:index.php');
            }
    
    
        }
        
   
    

?>