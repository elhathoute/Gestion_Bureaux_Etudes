<?php

    session_start();
    unset($_SESSION["user"]);
    // unset($_SESSION["user_id"]);
    unset($_COOKIE["logged_in"]);
    header("location:index.php");

?>