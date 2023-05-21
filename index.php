
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="images/BeplanLogo_2.png">

    <!-- CSS -->
    <!-- <link rel="stylesheet" href="css/bootstrap.min.css"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
</head>
<body>
    <div class="container">
  
        <div class="row  d-flex align-items-center justify-content-center">
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-lg login-panel">
                    <div class="card-header bg-white">
                            <!-- Company logo -->
                            <h1 class="text-center"  >
                                <img src="images/BeplanLogo.png" class="img-fluid"   alt="dummy image">
                            </h1>
                    </div>
                    <div class="card-body py-5 login">
                        <?php
                            session_start();
                            if(isset($_SESSION["error"])){
                                echo $_SESSION["error"];
                                unset($_SESSION["error"]);
                                
                            }
                        ?>
                        <form action="login.php" method="POST">
                        <label class="my-1 fs-5" for="username">Nom d'utilisateur :</label>

                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <input type="text" name="username" class="form-control required fw-normal " placeholder="Enter nom d'utilisateur" required  value="<?php if(!empty($_COOKIE["logged_in"])) echo $_COOKIE["logged_in"]?>">
                            </div>
                            <label class="my-1 fs-5" for="password">Mot de passe :</label>

                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <i class="fa-solid fa-lock"></i>
                                </div>
                                <input type="password" name="password" class="form-control required fw-normal " placeholder="Enter mot de passe" required value="<?php if(!empty($_COOKIE["logged_in_password"])) echo $_COOKIE["logged_in_password"]?>">
                                <!-- <div class="invalid-feedback">Please fill out this field</div> -->
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="remember-me" id="remember-me" class="form-check-input ">
                                <label for="remember-me" class="form-check-">Se souvenir de moi</label>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary col-12 fw-bold fs-5" style="background-color:#7B7DE5;border-color: #7B7DE5;">connexion</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- JS -->
<!-- <script src="js/bootstrap.min.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
</body>


</html>