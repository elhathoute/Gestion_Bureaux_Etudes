<?php

//define('DATABASE_HOST', getenv('IP'));
define('DATABASE_HOST', "localhost");
define('DATABASE_NAME', 'beplanDB');
define('DATABASE_USER', 'root');
define('DATABASE_PASS', '');


$cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);

if(session_status()=== PHP_SESSION_NONE){
    session_start();
}

if(mysqli_connect_errno()){
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}
?>